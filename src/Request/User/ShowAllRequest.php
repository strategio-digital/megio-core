<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Request\User;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Saas\Database\EntityManager;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;
use Nette\Schema\Expect;
use Saas\Security\Permissions\DefaultRole;

class ShowAllRequest implements IRequest
{
    public function __construct(
        private readonly EntityManager $em,
        private readonly Response      $response,
    )
    {
    }
    
    public function schema(): array
    {
        return [
            'currentPage' => Expect::int(1)->min(1)->required(),
            'itemsPerPage' => Expect::int(10)->max(1000)->required(),
            'orderBy' => Expect::array(Expect::structure([
                'col' => Expect::string('createdAt')->min(2),
                'desc' => Expect::bool(false)
            ]))
        ];
    }
    
    public function process(array $data): void
    {
        $repo = $this->em->getUserRepo();
        
        $qb = $repo->createQueryBuilder('U')
            ->select('U.id, R.name as role, U.email, U.createdAt, U.updatedAt')
            ->leftJoin('U.role', 'R')
            ->andWhere('R.name != :admin_role')
            ->setParameter('admin_role', DefaultRole::Admin->name());
        
        $count = (clone $qb)->select('count(U.id)')->getQuery()->getSingleScalarResult();
        
        $qb
            ->setFirstResult(($data['currentPage'] - 1) * $data['itemsPerPage'])
            ->setMaxResults($data['itemsPerPage']);
        
        if (array_key_exists('orderBy', $data)) {
            foreach ($data['orderBy'] as $param) {
                $qb->addOrderBy("U.{$param['col']}", $param['desc'] ? 'DESC' : 'ASC');
            }
        }
        
        $items = (new Paginator($qb->getQuery()->setHydrationMode(AbstractQuery::HYDRATE_ARRAY), false))
            ->getIterator()
            ->getArrayCopy();
        
        $this->response->send([
            'currentPage' => $data['currentPage'],
            'lastPage' => (int)ceil($count / $data['itemsPerPage']),
            'itemsPerPage' => $data['itemsPerPage'],
            'itemsCountAll' => $count,
            'items' => $items
        ]);
    }
}