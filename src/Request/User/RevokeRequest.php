<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Request\User;

use Saas\Database\EntityManager;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;
use Nette\Schema\Expect;
use Saas\Security\Permissions\DefaultRole;

class RevokeRequest implements IRequest
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
            'ids' => Expect::arrayOf('string')->required(),
        ];
    }
    
    public function process(array $data): void
    {
        $users = $this->em->getUserRepo()
            ->createQueryBuilder('U')
            ->select('U.id')
            ->leftJoin('U.role', 'R')
            ->andWhere('U.id IN (:ids)')
            ->andWhere('R.name != :admin_role')
            ->setParameter('admin_role', DefaultRole::Admin->name())
            ->setParameter('ids', $data['ids'])
            ->getQuery()
            ->getResult();
        
        $ids = array_map(fn($user) => $user['id'], $users);
        
        $this->em->getUserTokenRepo()
            ->createQueryBuilder('UT')
            ->update()
            ->set('UT.expiration', ':expiration')
            ->where('UT.user IN (:ids)')
            ->setParameter('ids', $ids)
            ->setParameter('expiration', (new \DateTime())->modify('-1 minute'))
            ->getQuery()
            ->execute();
        
        $this->response->send(['message' => "Users successfully revoked"]);
    }
}