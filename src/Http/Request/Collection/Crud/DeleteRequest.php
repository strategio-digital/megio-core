<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection\Crud;

use Nette\Schema\Expect;
use Saas\Database\EntityManager;
use Saas\Database\CrudHelper\CrudHelper;
use Saas\Event\CollectionEvent;
use Saas\Event\CollectionEvent\OnProcessingStartEvent;
use Saas\Event\CollectionEvent\OnProcessingFinishEvent;
use Symfony\Component\HttpFoundation\Response;

class DeleteRequest extends BaseCrudRequest
{
    public function __construct(protected readonly EntityManager $em, protected readonly CrudHelper $helper)
    {
    }
    
    public function schema(): array
    {
        $tables = array_map(fn($meta) => $meta['table'], $this->helper->getAllEntities());
        
        return [
            'table' => Expect::anyOf(...$tables)->required(),
            'ids' => Expect::arrayOf('string')->min(1)->required(),
        ];
    }
    
    public function process(array $data): Response
    {
        if (!$meta = $this->setUpMetadata($data['table'], false)) {
            return $this->error([$this->helper->getError()]);
        }
        
        $event = new OnProcessingStartEvent($data, $this->request, $meta);
        $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_START);
        
        $repo = $this->em->getRepository($meta->className);
        
        $qb = $repo->createQueryBuilder('E')
            ->where('E.id IN (:ids)')
            ->setParameter('ids', $data['ids']);
        
        $countRows = (clone $qb)->select('count(E.id)')->getQuery()->getSingleScalarResult();
        $countItems = count($data['ids']);
        $diff = $countItems - $countRows;
        
        if ($diff !== 0) {
            // TODO: how to handle this in event? - Add new event?
            return $this->error(["{$diff} of {$countItems} items you want to delete already does not exist"], 404);
        }
        
        $qb->delete()->getQuery()->execute();
        
        $result = [
            'ids' => $data['ids'],
            'message' => "Items successfully deleted"
        ];
        
        $response = $this->json($result);
        
        $event = new OnProcessingFinishEvent($data, $this->request, $meta, $result, $response);
        $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_FINISH);
        
        return $response;
    }
}