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
use Saas\Http\Response\Response;

class ShowOneRequest extends BaseCrudRequest
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
        
        return [
            'table' => Expect::anyOf(...$tables)->required(),
            'schema' => Expect::bool(false),
            'id' => Expect::string()->required(),
        ];
    }
    
    public function process(array $data): void
    {
        $meta = $this->setUpMetadata($data['table'], $data['schema'], CrudHelper::PROPERTY_SHOW_ONE);
        
        $repo = $this->em->getRepository($meta->className);
        
        $qb = $repo->createQueryBuilder('E')
            ->select($meta->getQuerySelect('E'))
            ->where('E.id = :id')
            ->setParameter('id', $data['id']);
        
        $item = $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        $response = ['item' => $item];
        
        if (!$item) {
            $this->response->sendError(['Item not found'], 404);
        }
        
        if ($data['schema']) {
            $response['schema'] = $meta->getSchema();
        }
        
        $this->response->send($response);
    }
}