<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\User;

use Doctrine\ORM\AbstractQuery;
use Saas\Database\EntityManager;
use Nette\Schema\Expect;
use Saas\Http\Request\Request;
use Saas\Security\Permissions\DefaultRole;
use Symfony\Component\HttpFoundation\Response;

class ShowOneRequest extends Request
{
    public function __construct(private readonly EntityManager $em)
    {
    }
    
    public function schema(): array
    {
        return ['id' => Expect::string()->required()];
    }
    
    public function process(array $data): Response
    {
        $repo = $this->em->getUserRepo();
        
        $qb = $repo->createQueryBuilder('U')
            ->select('U.id, R.name as role, U.email, U.createdAt, U.updatedAt, U.lastLogin')
            ->leftJoin('U.role', 'R')
            ->where('U.id = :id')
            ->andWhere('R.name != :admin_role OR U.role IS NULL')
            ->setParameter('admin_role', DefaultRole::Admin->name())
            ->setParameter('id', $data['id']);
        
        $result = $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        if (!$result) {
            return $this->error(["User id '{$data['id']}' not found"], 404);
        }
        
        return $this->json($result);
    }
}