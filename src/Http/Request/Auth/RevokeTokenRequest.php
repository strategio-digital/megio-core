<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Auth;

use Saas\Database\EntityManager;
use Nette\Schema\Expect;
use Saas\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class RevokeTokenRequest extends Request
{
    public function __construct(private readonly EntityManager $em)
    {
    }
    
    public function schema(): array
    {
        return ['user_ids' => Expect::arrayOf('string')->required()];
    }
    
    public function process(array $data): Response
    {
        $this->em->getUserTokenRepo()
            ->createQueryBuilder('UT')
            ->delete()
            ->where('UT.user IN (:ids)')
            ->setParameter('ids', $data['user_ids'])
            ->getQuery()
            ->execute();
        
        return $this->json(['message' => "Users successfully revoked"]);
    }
}