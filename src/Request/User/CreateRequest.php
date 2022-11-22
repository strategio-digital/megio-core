<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Request\User;

use Saas\Database\Entity\Role\Role;
use Saas\Database\Entity\User\User;
use Saas\Database\EntityManager;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;
use Saas\Security\Permissions\DefaultRole;
use Nette\Schema\Expect;

class CreateRequest implements IRequest
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
            'email' => Expect::email()->max(128)->required(),
            'password' => Expect::string()->min(6)->max(32)->required()
        ];
    }
    
    public function process(array $data): void
    {
        $repo = $this->em->getUserRepo();
        
        if ($repo->findOneBy(['email' => $data['email']])) {
            $this->response->sendError(["E-mail '{$data['email']}' already exists"]);
        }
        
        /** @var Role|null $role */
        $role = $this->em->getRoleRepo()->findOneBy(['name' => DefaultRole::Registered->name()]);
        
        if (!$role) {
            $this->response->sendError(["Permission 'user' does not exists"]);
        }
        
        $user = (new User())
            ->setEmail($data['email'])
            ->setPassword($data['password'])
            ->setRole($role);
        
        $this->em->persist($user);
        $this->em->flush();
        
        $this->response->send(['message' => "User '{$user->getEmail()}' successfully created"]);
    }
}