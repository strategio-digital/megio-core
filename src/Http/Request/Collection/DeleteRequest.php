<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Megio\Collection\RecipeFinder;
use Megio\Database\EntityManager;
use Megio\Event\Collection\Events;
use Megio\Event\Collection\EventType;
use Megio\Event\Collection\OnExceptionEvent;
use Megio\Event\Collection\OnFinishEvent;
use Megio\Event\Collection\OnStartEvent;
use Megio\Http\Request\AbstractRequest;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function array_map;
use function count;

class DeleteRequest extends AbstractRequest
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder $recipeFinder,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        $recipeKeys = array_map(fn(
            $r,
        ) => $r->key(), $this->recipeFinder->load()->getAll());

        return [
            'recipeKey' => Expect::anyOf(...$recipeKeys)->required(),
            'ids' => Expect::arrayOf('string')->min(1)->required(),
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws NotSupported
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function processValidatedData(array $data): Response
    {
        /** @noinspection DuplicatedCode */
        $recipe = $this->recipeFinder->findByKey($data['recipeKey']);

        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipeKey']}' not found"]);
        }

        $event = new OnStartEvent(EventType::DELETE, $data, $recipe, $this->request);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_START->value);

        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }

        $repo = $this->em->getRepository($recipe->source());

        $qb = $repo->createQueryBuilder('entity')
            ->where('entity.id IN (:ids)')
            ->setParameter('ids', $data['ids']);

        $countRows = (int)(clone $qb)->select('count(entity.id)')->getQuery()->getSingleScalarResult();
        $countItems = count($data['ids']);
        $diff = $countItems - $countRows;

        if ($diff !== 0) {
            $e = new NotFoundHttpException("{$diff} of {$countItems} items you want to delete already does not exist");
            $response = $this->error([$e->getMessage()], 404);
            $event = new OnExceptionEvent(EventType::DELETE, $data, $recipe, $e, $this->request, $response);
            $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
            return $dispatcher->getResponse();
        }

        try {
            $qb->delete()->getQuery()->execute();
        } catch (ConstraintViolationException $e) {
            $response = $this->error([$e->getMessage()], 400);
            $event = new OnExceptionEvent(EventType::DELETE, $data, $recipe, $e, $this->request, $response);
            $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
            return $dispatcher->getResponse();
        }

        $result = [
            'ids' => $data['ids'],
            'message' => "Items successfully deleted",
        ];

        $response = $this->json($result);

        $event = new OnFinishEvent(EventType::DELETE, $data, $recipe, $result, $this->request, $response);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_FINISH->value);

        return $dispatcher->getResponse();
    }

    public function process(Request $request): Response
    {
        return new Response('ProcessValidatedData() is deprecated', 500);
    }
}
