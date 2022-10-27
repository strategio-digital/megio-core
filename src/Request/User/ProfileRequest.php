<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Request\User;

use Framework\Database\EntityManager;
use Framework\Guard\Auth;
use Framework\Http\Request\IRequest;
use Framework\Http\Response\Response;
use Doctrine\ORM\AbstractQuery;

class ProfileRequest implements IRequest
{
    public function __construct(
        private readonly Response      $response,
        private readonly EntityManager $em,
        private readonly Auth          $auth,
    )
    {
    }
    
    public function schema(): array
    {
        return [];
    }
    
    public function process(array $data): void
    {
        $user = $this->em->getUserRepo()
            ->createQueryBuilder('User')
            ->select('User.id, User.email, Role.name as role')
            ->innerJoin('User.role', 'Role')
            ->where('User.id = :id')
            ->setParameter('id', $this->auth->getUser()->getId())
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        $this->response->send($user);
    }
}