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

class DeleteRequest implements IRequest
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
            'id' => Expect::string()->required(),
        ];
    }
    
    public function process(array $data): void
    {
        $repo = $this->em->getUserRepo();
        
        $qb = $repo->createQueryBuilder('U')
            ->select('U')
            ->leftJoin('U.role', 'R')
            ->where('U.id = :id')
            ->andWhere('R.name != :admin_role')
            ->setParameter('admin_role', DefaultRole::Admin->name())
            ->setParameter('id', $data['id']);
        
        $user = $qb->getQuery()->getOneOrNullResult();
        
        if (!$user) {
            $this->response->sendError(["User id '${data['id']}' not found"], 404);
        }
    
        $this->em->remove($user);
        $this->em->flush();
        
        $this->response->send(['message' => "User id '{$data['id']}' successfully deleted"]);
    }
}