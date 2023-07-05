<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\User;

use Saas\Database\EntityManager;
use Nette\Schema\Expect;
use Saas\Http\Request\Request;
use Saas\Security\Permissions\DefaultRole;
use Symfony\Component\HttpFoundation\Response;

class DeleteRequest extends Request
{
    public function __construct(private readonly EntityManager $em)
    {
    }
    
    public function schema(): array
    {
        return ['ids' => Expect::arrayOf('string')->required()];
    }
    
    public function process(array $data): Response
    {
        $repo = $this->em->getUserRepo();
        
        $users = $repo->createQueryBuilder('U')
            ->select('U.id')
            ->leftJoin('U.role', 'R')
            ->andWhere('U.id IN (:ids)')
            ->andWhere('R.name != :admin_role OR U.role IS NULL')
            ->setParameter('admin_role', DefaultRole::Admin->name())
            ->setParameter('ids', $data['ids'])
            ->getQuery()->getResult();
        
        $ids = array_map(fn($user) => $user['id'], $users);
        
        $qb = $repo->createQueryBuilder('U')
            ->delete()
            ->andWhere('U.id IN (:ids)')
            ->setParameter('ids', $ids);
        
        $qb->getQuery()->execute();
        
        return $this->json(['message' => "Users successfully deleted"]);
    }
}