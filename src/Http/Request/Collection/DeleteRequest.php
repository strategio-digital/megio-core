<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection;

use Nette\Schema\Expect;
use Saas\Database\EntityManager;
use Saas\Database\CrudHelper\CrudHelper;
use Saas\Event\Collection\CollectionEvent;
use Saas\Event\Collection\OnProcessingExceptionEvent;
use Saas\Event\Collection\OnProcessingStartEvent;
use Saas\Event\Collection\OnProcessingFinishEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_START);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $repo = $this->em->getRepository($meta->className);
        
        $qb = $repo->createQueryBuilder('E')
            ->where('E.id IN (:ids)')
            ->setParameter('ids', $data['ids']);
        
        $countRows = (clone $qb)->select('count(E.id)')->getQuery()->getSingleScalarResult();
        $countItems = count($data['ids']);
        $diff = $countItems - $countRows;
        
        if ($diff !== 0) {
            $e = new NotFoundHttpException("{$diff} of {$countItems} items you want to delete already does not exist");
            $response = $this->error([$e->getMessage()], 404);
            $event = new OnProcessingExceptionEvent($data, $this->request, $meta, $e, $response);
            $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
            return $dispatcher->getResponse();
        }
        
        $qb->delete()->getQuery()->execute();
        
        $result = [
            'ids' => $data['ids'],
            'message' => "Items successfully deleted"
        ];
        
        $response = $this->json($result);
        
        $event = new OnProcessingFinishEvent($data, $this->request, $meta, $result, $response);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_FINISH);
        
        return $dispatcher->getResponse();
    }
}