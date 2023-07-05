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
use Symfony\Component\HttpFoundation\Response;

class CreateRequest extends BaseCrudRequest
{
    public function __construct(protected readonly EntityManager $em, protected readonly CrudHelper $helper)
    {
    }
    
    public function schema(): array
    {
        $tables = array_map(fn($meta) => $meta['table'], $this->helper->getAllEntityClassNames());
        
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
        
        $ids = [];
        
        foreach ($data['rows'] as $row) {
            /** @var \Saas\Database\Interface\CrudEntity $entity */
            $entity = new $meta->className();
            
            try {
                $entity = $this->helper->setUpEntityProps($entity, $row);
                $this->em->persist($entity);
                $ids[] = $entity->getId();
            } catch (CrudException|EntityException $e) {
                return $this->error([$e->getMessage()], 406);
            }
            
            $this->em->beginTransaction();
            
            try {
                $this->em->flush();
                $this->em->commit();
            } catch (UniqueConstraintViolationException $e) {
                $this->em->rollback();
                return $this->error([$e->getMessage()]);
            } catch (\Exception $e) {
                $this->em->rollback();
                throw $e;
            }
        }
        
        return $this->json([
            'ids' => $ids,
            'message' => "Items successfully created"
        ]);
    }
}