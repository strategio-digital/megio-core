<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection;

use Saas\Database\CrudHelper\CrudHelper;
use Saas\Database\EntityManager;
use Nette\Schema\Expect;
use Saas\Event\Collection\CollectionEvent;
use Saas\Event\Collection\OnProcessingStartEvent;
use Saas\Event\Collection\OnProcessingFinishEvent;
use Symfony\Component\HttpFoundation\Response;

class ShowRequest extends BaseCrudRequest
{
    public function __construct(protected readonly EntityManager $em, protected readonly CrudHelper $helper)
    {
    }
    
    public function schema(): array
    {
        $tables = array_map(fn($meta) => $meta['table'], $this->helper->getAllEntities());
        
        return [
            'table' => Expect::anyOf(...$tables)->required(),
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
        if (!$meta = $this->setUpMetadata($data['table'], $data['schema'], CrudHelper::PROPERTY_SHOW_ALL)) {
            return $this->error([$this->helper->getError()]);
        }
        
        $event = new OnProcessingStartEvent($data, $this->request, $meta);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_START);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $repo = $this->em->getRepository($meta->className);
        
        $qb = $repo->createQueryBuilder('E')
            ->select($meta->getQuerySelect('E'));
        
        $count = (clone $qb)->select('count(E.id)')->getQuery()->getSingleScalarResult();
        
        $qb->setFirstResult(($data['currentPage'] - 1) * $data['itemsPerPage'])
            ->setMaxResults($data['itemsPerPage']);
        
        foreach ($data['orderBy'] as $param) {
            $qb->addOrderBy("E.{$param['col']}", $param['desc'] ? 'DESC' : 'ASC');
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
            $result['schema'] = $meta->getSchema();
        }
        
        $response = $this->json($result);
        
        $event = new OnProcessingFinishEvent($data, $this->request, $meta, $result, $response);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_FINISH);
        
        return $dispatcher->getResponse();
    }
}