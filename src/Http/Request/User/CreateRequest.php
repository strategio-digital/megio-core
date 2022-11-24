<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\User;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
            'rows' => Expect::arrayOf(Expect::structure([
                'email' => Expect::email()->max(128)->required(),
                'password' => Expect::string()->min(6)->max(32)->required()
            ]))->min(1)->max(20)->required() // Limit 20 because Argon2 calculating password hash for 1sec
        ];
    }
    
    public function process(array $data): void
    {
        /** @var Role|null $role */
        $role = $this->em->getRoleRepo()->findOneBy(['name' => DefaultRole::Registered->name()]);
    
        if (!$role) {
            $this->response->sendError(["Permission 'user' does not exists"]);
        }
        
        foreach ($data['rows'] as $row) {
            $user = new User();
            $user->setEmail($row['email']);
            $user->setPassword($row['password']);
            $user->setRole($role);
            $this->em->persist($user);
        }
        
        $this->em->beginTransaction();
        
        try {
            $this->em->flush();
            $this->em->commit();
        } catch (UniqueConstraintViolationException $e) {
            $this->em->rollback();
            $this->response->sendError([$e->getMessage()]);
        }  catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
        
        $this->response->send(['message' => "Users successfully created"]);
    }
}