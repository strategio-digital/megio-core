<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Doctrine\ORM\AbstractQuery;
use Megio\Collection\CollectionRequest;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\ReadBuilder\ReadBuilderEvent;
use Megio\Collection\RecipeFinder;
use Megio\Collection\SchemaFormatter;
use Megio\Event\Collection\EventType;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Megio\Database\EntityManager;
use Megio\Event\Collection\Events;
use Megio\Event\Collection\OnExceptionEvent;
use Megio\Event\Collection\OnStartEvent;
use Megio\Event\Collection\OnFinishEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReadRequest extends Request
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder  $recipeFinder,
        protected readonly ReadBuilder   $readBuilder,
    )
    {
    }
    
    public function schema(array $data): array
    {
        $recipeKeys = array_map(fn($r) => $r->key(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipeKey' => Expect::anyOf(...$recipeKeys)->required(),
            'id' => Expect::string()->required(),
            'schema' => Expect::bool(false),
            'adminPanel' => Expect::bool(false),
            'custom_data' => Expect::arrayOf('int|float|string|bool|null|array', 'string')->nullable()->default([]),
        ];
    }
    
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByKey($data['recipeKey']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipeKey']}' not found"]);
        }
        
        $collectionRequest = new CollectionRequest($this->request, false, $data, null, []);
        
        try {
            $defaultBuilder = $this->readBuilder->create($recipe, ReadBuilderEvent::READ_ONE);
            $builder = $recipe->read($defaultBuilder, $collectionRequest)->build();
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }
        
        /** @noinspection DuplicatedCode */
        if ($builder->countFields() === 1) {
            return $this->error(["Collection '{$data['recipe']}' has no readable fields"]);
        }
        
        $event = new OnStartEvent(EventType::READ, $data, $recipe, $this->request);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_START->value);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $repo = $this->em->getRepository($recipe->source());
        $qb = $builder->createQueryBuilder($repo, 'entity')
            ->where('entity.id = :id')
            ->setParameter('id', $data['id']);
        
        $item = $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        /** @noinspection DuplicatedCode */
        if (!$item) {
            $e = new NotFoundHttpException("Item '{$data['id']}' not found");
            $response = $this->error([$e->getMessage()], 404);
            $event = new OnExceptionEvent(EventType::READ, $data, $recipe, $e, $this->request, $response);
            $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
            return $dispatcher->getResponse();
        }
        
        $result = ['item' => $builder->format($item, $data['adminPanel'])];
        
        /** @noinspection DuplicatedCode */
        if ($data['schema']) {
            $result['schema'] = SchemaFormatter::format($recipe, $builder);
        }
        
        $response = $this->json($result);
        
        $event = new OnFinishEvent(EventType::READ, $data, $recipe, $result, $this->request, $response);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_FINISH->value);
        
        return $dispatcher->getResponse();
    }
}