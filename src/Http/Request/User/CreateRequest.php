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
use Saas\Http\Request\Request;
use Saas\Security\Permissions\DefaultRole;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Response;

class CreateRequest extends Request
{
    public function __construct(private readonly EntityManager $em)
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
    
    public function process(array $data): Response
    {
        /** @var Role|null $role */
        $role = $this->em->getRoleRepo()->findOneBy(['name' => DefaultRole::User->name()]);
        
        if (!$role) {
            return $this->error(["Permission 'user' does not exists"]);
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
            return $this->error([$e->getMessage()]);
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
        
        return $this->json(['message' => "Users successfully created"]);
    }
}