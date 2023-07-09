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
        
        $repo = $this->em->getRepository($meta->className);
        
        $repo->createQueryBuilder('E')
            ->delete()
            ->where('E.id IN (:ids)')
            ->setParameter('ids', $data['ids'])
            ->getQuery()
            ->execute();
        
        return $this->json([
            'ids' => $data['ids'],
            'message' => "Items successfully deleted"
        ]);
    }
}