<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection\Crud;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Schema\Expect;
use Saas\Database\CrudHelper\CrudException;
use Saas\Database\Entity\EntityException;
use Saas\Database\EntityManager;
use Saas\Database\CrudHelper\CrudHelper;
use Saas\Event\CollectionEvent;
use Saas\Event\CollectionEvent\OnProcessingFinishEvent;
use Saas\Event\CollectionEvent\OnProcessingStartEvent;
use Saas\Event\CollectionEvent\OnProcessingExceptionEvent;
use Symfony\Component\HttpFoundation\Response;

class UpdateRequest extends BaseCrudRequest
{
    public function __construct(protected readonly EntityManager $em, protected readonly CrudHelper $helper)
    {
    }
    
    public function schema(): array
    {
        $tables = array_map(fn($meta) => $meta['table'], $this->helper->getAllEntities());
        
        // TODO: get rows types trough reflection and validate them
        
        return [
            'table' => Expect::anyOf(...$tables)->required(),
            'rows' => Expect::arrayOf(
                Expect::structure([
                    'id' => Expect::string()->required(),
                    'data' => Expect::arrayOf('int|float|string|bool', 'string')->min(1)->required()
                ])->castTo('array')
            )->min(1)->required()
        ];
    }
    
    public function process(array $data): Response
    {
        if (!$meta = $this->setUpMetadata($data['table'], false)) {
            return $this->error([$this->helper->getError()]);
        }
        
        $event = new OnProcessingStartEvent($data, $this->request, $meta);
        $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_START);
        
        $ids = array_map(fn($row) => $row['id'], $data['rows']);
        
        $qb = $this->em->getRepository($meta->className)
            ->createQueryBuilder('E')
            ->select('E')
            ->where('E.id IN (:ids)')
            ->setParameter('ids', $ids);
        
        /** @var \Saas\Database\Interface\ICrudable[] $rows */
        $rows = $qb->getQuery()->getResult();
        
        foreach ($data['rows'] as $row) {
            $dbRow = current(array_filter($rows, fn($db) => $db->getId() === $row['id']));
            if (!$dbRow) {
                // TODO: how to handle this in event? - Add new event?
                return $this->error(["Item '{$row['id']}' not found"], 404);
            }
            try {
                $this->helper->setUpEntityProps($dbRow, $row['data']);
            } catch (CrudException|EntityException $e) {
                $response = $this->error([$e->getMessage()], 406);
                $event = new OnProcessingExceptionEvent($data, $this->request, $meta, $e, $response);
                $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
                return $response;
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
            $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
            return $response;
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
        
        $result = [
            'ids' => $ids,
            'message' => "Items successfully updated"
        ];
        
        $response = $this->json($result);
        
        $event = new OnProcessingFinishEvent($data, $this->request, $meta, $result, $response);
        $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_FINISH);
        
        return $response;
    }
}