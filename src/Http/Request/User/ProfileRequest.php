<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\User;

use Saas\Database\EntityManager;
use Saas\Http\Request\Auth;
use Saas\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileRequest extends Request
{
    public function __construct(protected readonly EntityManager $em, protected readonly Auth $auth)
    {
    }
    
    public function schema(): array
    {
        return [];
    }
    
    public function process(array $data): Response
    {
        // TODO: update this logic
//        $user = $this->em->getUserRepo()
//            ->createQueryBuilder('User')
//            ->select('User.id, User.email, Role.name as role, User.lastLogin, User.createdAt')
//            ->innerJoin('User.role', 'Role')
//            ->where('User.id = :id')
//            ->setParameter('id', $this->auth->getUser()->getId())
//            ->getQuery()
//            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
//
//        return $this->json($user);
        return $this->json();
    }
}