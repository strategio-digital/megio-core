<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\ReadBuilder\ReadBuilderEvent;
use Megio\Collection\RecipeFinder;
use Megio\Collection\SchemaFormatter;
use Megio\Database\EntityManager;
use Megio\Event\Collection\EventType;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Megio\Event\Collection\Events;
use Megio\Event\Collection\OnStartEvent;
use Megio\Event\Collection\OnFinishEvent;
use Symfony\Component\HttpFoundation\Response;

class ReadAllRequest extends Request
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder  $recipeFinder,
        protected readonly ReadBuilder   $readBuilder,
    )
    {
    }
    
    public function schema(): array
    {
        $recipeKeys = array_map(fn($r) => $r->key(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$recipeKeys)->required(),
            'schema' => Expect::bool(false),
            'adminPanel' => Expect::bool(false),
            'currentPage' => Expect::int(1)->min(1)->required(),
            'itemsPerPage' => Expect::int(10)->max(1000)->required(),
            'orderBy' => Expect::arrayOf(Expect::structure([
                'col' => Expect::string()->required(),
                'desc' => Expect::bool()->required()
            ])->castTo('array'))->min(1)->default([['col' => 'createdAt', 'desc' => true]])
        ];
    }
    
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NoResultException
     */
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByKey($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        try {
            $builder = $recipe->readAll($this->readBuilder->create($recipe, ReadBuilderEvent::READ_ONE))->build();
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }
        
        
        /** @noinspection DuplicatedCode */
        if ($builder->countFields() === 1) {
            return $this->error(["Collection '{$data['recipe']}' has no readable fields"]);
        }
        
        $event = new OnStartEvent(EventType::READ_ALL, $data, $recipe, $this->request);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_START->value);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $repo = $this->em->getRepository($recipe->source());
        
        $qb = $repo->createQueryBuilder('entity')
            ->select($builder->getQbSelect('entity'));
        
        $count = (clone $qb)->select('count(entity.id)')->getQuery()->getSingleScalarResult();
        
        $qb->setFirstResult(($data['currentPage'] - 1) * $data['itemsPerPage'])
            ->setMaxResults($data['itemsPerPage']);
        
        foreach ($data['orderBy'] as $param) {
            $qb->addOrderBy("entity.{$param['col']}", $param['desc'] ? 'DESC' : 'ASC');
        }
        
        $items = $qb->getQuery()->getArrayResult();
        foreach ($items as $key => $item) {
            $items[$key] = $builder->format($item, $data['adminPanel']);
        }
        
        $result = [
            'pagination' => [
                'currentPage' => $data['currentPage'],
                'lastPage' => (int)ceil($count / $data['itemsPerPage']),
                'itemsPerPage' => $data['itemsPerPage'],
                'itemsCountAll' => $count,
            ],
            'items' => $items
        ];
        
        /** @noinspection DuplicatedCode */
        if ($data['schema']) {
            $result['schema'] = SchemaFormatter::format($recipe, $builder);
        }
        
        $response = $this->json($result);
        
        $event = new OnFinishEvent(EventType::READ_ALL, $data, $recipe, $result, $this->request, $response);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_FINISH->value);
        
        return $dispatcher->getResponse();
    }
}