<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Request\User;

use Saas\Database\EntityManager;
use Saas\Guard\Auth;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;
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