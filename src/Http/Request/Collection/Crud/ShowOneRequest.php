<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection\Crud;

use Doctrine\ORM\AbstractQuery;
use Nette\Schema\Expect;
use Saas\Database\EntityManager;
use Saas\Database\CrudHelper\CrudHelper;
use Symfony\Component\HttpFoundation\Response;

class ShowOneRequest extends BaseCrudRequest
{
    public function __construct(protected readonly EntityManager $em, protected readonly CrudHelper $helper)
    {
    }
    
    public function schema(): array
    {
        $tables = array_map(fn($meta) => $meta['table'], $this->helper->getAllEntityClassNames());
        
        return [
            'table' => Expect::anyOf(...$tables)->required(),
            'schema' => Expect::bool(false),
            'id' => Expect::string()->required(),
        ];
    }
    
    public function process(array $data): Response
    {
        if (!$meta = $this->setUpMetadata($data['table'], $data['schema'], CrudHelper::PROPERTY_SHOW_ONE)) {
            return $this->error([$this->helper->getError()]);
        }
        
        $repo = $this->em->getRepository($meta->className);
        
        $qb = $repo->createQueryBuilder('E')
            ->select($meta->getQuerySelect('E'))
            ->where('E.id = :id')
            ->setParameter('id', $data['id']);
        
        $item = $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        $response = ['item' => $item];
        
        if (!$item) {
            return $this->error(['Item not found'], 404);
        }
        
        if ($data['schema']) {
            $response['schema'] = $meta->getSchema();
        }
        
        return $this->json($response);
    }
}