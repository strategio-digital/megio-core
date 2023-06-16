<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Auth;

use Saas\Database\EntityManager;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;
use Nette\Schema\Expect;
use Saas\Security\Permissions\DefaultRole;

class RevokeTokenRequest implements IRequest
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
            'user_ids' => Expect::arrayOf('string')->required(),
        ];
    }
    
    public function process(array $data): void
    {
        $this->em->getUserTokenRepo()
            ->createQueryBuilder('UT')
            ->delete()
            ->where('UT.user IN (:ids)')
            ->setParameter('ids', $data['user_ids'])
            ->getQuery()
            ->execute();
        
        $this->response->send(['message' => "Users successfully revoked"]);
    }
}