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
use Saas\Database\EntityManager;
use Saas\Database\CrudHelper\CrudHelper;
use Saas\Http\Response\Response;

class UpdateRequest extends BaseCrudRequest
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly CrudHelper    $helper,
        protected readonly Response      $response
    )
    {
    }
    
    public function schema(): array
    {
        $tables = array_map(fn($meta) => $meta['table'], $this->helper->getAllEntityClassNames());
        
        // TODO: get rows types trough reflection and validate them
        
        return [
            'table' => Expect::anyOf(...$tables)->required(),
            'rows' => Expect::arrayOf(
                Expect::structure([
                    'id' => Expect::string()->required(),
                    'data' => Expect::array()->min(1)->required()
                ])
            )->min(1)->required()
        ];
    }
    
    public function process(array $data): void
    {
        $meta = $this->setUpMetadata($data['table'], false);
        $ids = array_map(fn($row) => $row['id'], $data['rows']);
        
        $qb = $this->em->getRepository($meta->className)
            ->createQueryBuilder('E')
            ->select('E')
            ->where('E.id IN (:ids)')
            ->setParameter('ids', $ids);
        
        /** @var \Saas\Database\Interface\CrudEntity[] $rows */
        $rows = $qb->getQuery()->getResult();
        
        foreach ($data['rows'] as $row) {
            $dbRow = current(array_filter($rows, fn($db) => $db->getId() === $row['id']));
            if (!$dbRow) {
                $this->response->sendError(["Item '{$row['id']}' not found"], 404);
            }
            try {
                $this->helper->setUpEntityProps($dbRow, $row['data']);
            } catch (CrudException $e) {
                $this->response->sendError([$e->getMessage()], 406);
            }
        }
        
        $this->em->beginTransaction();

        try {
            $this->em->flush();
            $this->em->commit();
        } catch (UniqueConstraintViolationException $e) {
            $this->em->rollback();
            $this->response->sendError([$e->getMessage()]);
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
        
        $this->response->send([
            'ids' => $ids,
            'message' => "Items successfully updated"
        ]);
    }
}