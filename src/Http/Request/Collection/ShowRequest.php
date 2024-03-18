<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Megio\Collection\CollectionException;
use Megio\Collection\CollectionPropType;
use Megio\Collection\RecipeFinder;
use Megio\Database\EntityManager;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Megio\Event\Collection\CollectionEvent;
use Megio\Event\Collection\OnProcessingStartEvent;
use Megio\Event\Collection\OnProcessingFinishEvent;
use Symfony\Component\HttpFoundation\Response;

class ShowRequest extends Request
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder  $recipeFinder,
    )
    {
    }
    
    public function schema(): array
    {
        $names = array_map(fn($r) => $r->name(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$names)->required(),
            'schema' => Expect::bool(false),
            'currentPage' => Expect::int(1)->min(1)->required(),
            'itemsPerPage' => Expect::int(10)->max(1000)->required(),
            'orderBy' => Expect::arrayOf(Expect::structure([
                'col' => Expect::string()->required(),
                'desc' => Expect::bool()->required()
            ])->castTo('array'))->min(1)->default([['col' => 'createdAt', 'desc' => true]])
        ];
    }
    
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByName($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        try {
            $metadata = $recipe->getEntityMetadata( CollectionPropType::READ_ALL);
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }
        
        $event = new OnProcessingStartEvent($data, $this->request, $metadata);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_START);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $repo = $this->em->getRepository($recipe->source());
        
        $qb = $repo->createQueryBuilder('entity')
            ->select($metadata->getQbSelect('entity'));
        
        $count = (clone $qb)->select('count(entity.id)')->getQuery()->getSingleScalarResult();
        
        $qb->setFirstResult(($data['currentPage'] - 1) * $data['itemsPerPage'])
            ->setMaxResults($data['itemsPerPage']);
        
        foreach ($data['orderBy'] as $param) {
            $qb->addOrderBy("entity.{$param['col']}", $param['desc'] ? 'DESC' : 'ASC');
        }
        
        $result = [
            'pagination' => [
                'currentPage' => $data['currentPage'],
                'lastPage' => (int)ceil($count / $data['itemsPerPage']),
                'itemsPerPage' => $data['itemsPerPage'],
                'itemsCountAll' => $count,
            ],
            'items' => $qb->getQuery()->getArrayResult()
        ];
        
        if ($data['schema']) {
            $result['schema'] = $metadata->getSchema();
        }
        
        $response = $this->json($result);
        
        $event = new OnProcessingFinishEvent($data, $this->request, $metadata, $result, $response);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_FINISH);
        
        return $dispatcher->getResponse();
    }
}