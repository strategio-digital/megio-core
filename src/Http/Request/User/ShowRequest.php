<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\User;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Saas\Database\EntityManager;
use Nette\Schema\Expect;
use Saas\Http\Request\Request;
use Saas\Security\Permissions\DefaultRole;
use Symfony\Component\HttpFoundation\Response;

class ShowRequest extends Request
{
    public function __construct(private readonly EntityManager $em)
    {
    }
    
    public function schema(): array
    {
        return [
            'currentPage' => Expect::int(1)->min(1)->required(),
            'itemsPerPage' => Expect::int(10)->max(1000)->required(),
            'orderBy' => Expect::arrayOf(Expect::structure([
                'col' => Expect::string()->required(),
                'desc' => Expect::bool()->required()
            ]))->min(1)->default([['col' => 'createdAt', 'desc' => true]])
        ];
    }
    
    public function process(array $data): Response
    {
        $repo = $this->em->getUserRepo();
        
        $qb = $repo->createQueryBuilder('U')
            ->select('U.id, R.name as role, U.email, U.createdAt, U.updatedAt')
            ->leftJoin('U.role', 'R')
            ->where('R.name != :admin_role OR U.role IS NULL')
            ->setParameter('admin_role', DefaultRole::Admin->name());
        
        $count = (clone $qb)->select('count(U.id)')->getQuery()->getSingleScalarResult();
        
        $qb
            ->setFirstResult(($data['currentPage'] - 1) * $data['itemsPerPage'])
            ->setMaxResults($data['itemsPerPage']);
        
        foreach ($data['orderBy'] as $param) {
            $qb->addOrderBy("U.{$param['col']}", $param['desc'] ? 'DESC' : 'ASC');
        }
        
        $users = (new Paginator($qb->getQuery()->setHydrationMode(AbstractQuery::HYDRATE_ARRAY), false))->getIterator();
        $users = $users instanceof \ArrayIterator ? $users->getArrayCopy() : iterator_to_array($users);
        
        $qb2 = $this->em->getUserTokenRepo()->createQueryBuilder('UT')
            ->select('U.id as id, U.lastLogin as lastLogin, UT.expiration as loginExpiration')
            ->leftJoin('UT.user', 'U')
            ->where('UT.user IN (:ids)')
            ->setParameter('ids', array_map(fn($user) => $user['id'], $users));
        
        $tokens = $qb2->getQuery()->getArrayResult();
        
        foreach ($users as $key => $user) {
            $token = current(array_filter($tokens, fn($token) => $token['id'] === $user['id']));
            $users[$key] = $token ? array_merge($user, $token) : array_merge($user, ['loginExpiration' => null, 'lastLogin' => null]);
        }
        
        return $this->json([
            'currentPage' => $data['currentPage'],
            'lastPage' => (int)ceil($count / $data['itemsPerPage']),
            'itemsPerPage' => $data['itemsPerPage'],
            'itemsCountAll' => $count,
            'items' => $users
        ]);
    }
}