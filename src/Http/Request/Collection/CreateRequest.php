<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Schema\Expect;
use Megio\Database\CrudHelper\CrudException;
use Megio\Database\Entity\EntityException;
use Megio\Database\EntityManager;
use Megio\Database\CrudHelper\CrudHelper;
use Megio\Event\Collection\CollectionEvent;
use Megio\Event\Collection\OnProcessingStartEvent;
use Megio\Event\Collection\OnProcessingExceptionEvent;
use Megio\Event\Collection\OnProcessingFinishEvent;
use Symfony\Component\HttpFoundation\Response;

class CreateRequest extends BaseCrudRequest
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly CrudHelper    $helper,
    )
    {
    }
    
    public function schema(): array
    {
        $tables = array_map(fn($meta) => $meta['table'], $this->helper->getAllEntities());
        
        // TODO: get rows types trough reflection and validate them
        
        return [
            'table' => Expect::anyOf(...$tables)->required(),
            'rows' => Expect::array()->min(1)->max(1000)->required()
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
        
        $ids = [];
        
        foreach ($data['rows'] as $row) {
            /** @var \Megio\Database\Interface\ICrudable $entity */
            $entity = new $meta->className();
            
            try {
                $entity = $this->helper->setUpEntityProps($entity, $row);
                $this->em->persist($entity);
                $ids[] = $entity->getId();
            } catch (CrudException|EntityException $e) {
                $response = $this->error([$e->getMessage()], 406);
                $event = new OnProcessingExceptionEvent($data, $this->request, $meta, $e, $response);
                $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
                return $dispatcher->getResponse();
            }
        }
        
        $this->em->beginTransaction();
        
        try {
            $this->em->flush();
            $this->em->commit();
        } catch (UniqueConstraintViolationException $e) {
            $this->em->rollback();
            $response = $this->error([$e->getMessage()]);
            $event = new OnProcessingExceptionEvent($data, $this->request, $meta, $e, $response);
            $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
            return $dispatcher->getResponse();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
        
        $result = [
            'ids' => $ids,
            'message' => "Items successfully created"
        ];
        
        $response = $this->json($result);
        
        $event = new OnProcessingFinishEvent($data, $this->request, $meta, $result, $response);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_FINISH);
        
        return $dispatcher->getResponse();
    }
}