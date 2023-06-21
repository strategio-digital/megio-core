<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection\Crud;

use Saas\Database\CrudHelper\CrudHelper;
use Saas\Database\EntityManager;
use Saas\Http\Response\Response;
use Nette\Schema\Expect;

class ShowRequest extends BaseCrudRequest
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly CrudHelper    $helper,
        protected readonly Response      $response,
    )
    {
    }
    
    public function schema(): array
    {
        $tables = array_map(fn($meta) => $meta['table'], $this->helper->getAllEntityClassNames());
        
        return [
            'table' => Expect::anyOf(...$tables)->required(),
            'schema' => Expect::bool(false),
            'currentPage' => Expect::int(1)->min(1)->required(),
            'itemsPerPage' => Expect::int(10)->max(1000)->required(),
            'orderBy' => Expect::arrayOf(Expect::structure([
                'col' => Expect::string()->required(),
                'desc' => Expect::bool()->required()
            ]))->min(1)->default([['col' => 'createdAt', 'desc' => true]])
        ];
    }
    
    public function process(array $data): void
    {
        $meta = $this->setUpMetadata($data['table'], $data['schema'], CrudHelper::PROPERTY_SHOW_ALL);
        $repo = $this->em->getRepository($meta->className);
        
        $qb = $repo->createQueryBuilder('E')
            ->select($meta->getQuerySelect('E'));
        
        $count = (clone $qb)->select('count(E.id)')->getQuery()->getSingleScalarResult();
        
        $qb->setFirstResult(($data['currentPage'] - 1) * $data['itemsPerPage'])
            ->setMaxResults($data['itemsPerPage']);
        
        foreach ($data['orderBy'] as $param) {
            $qb->addOrderBy("E.{$param['col']}", $param['desc'] ? 'DESC' : 'ASC');
        }
        
        $response = [
            'pagination' => [
                'currentPage' => $data['currentPage'],
                'lastPage' => (int)ceil($count / $data['itemsPerPage']),
                'itemsPerPage' => $data['itemsPerPage'],
                'itemsCountAll' => $count,
            ],
            'items' => $qb->getQuery()->getArrayResult()
        ];
        
        if ($data['schema']) {
            $response['schema'] = $meta->getSchema();
        }
        
        $this->response->send($response);
    }
}