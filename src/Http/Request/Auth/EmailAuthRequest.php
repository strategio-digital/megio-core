<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Auth;

use Nette\Security\Passwords;
use Saas\Database\CrudHelper\CrudHelper;
use Saas\Database\Entity\Admin;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\Entity\Auth\Token;
use Saas\Database\EntityManager;
use Saas\Database\Interface\AuthUser;
use Saas\Http\Request\Request;
use Saas\Security\JWT\ClaimsFormatter;
use Saas\Security\JWT\JWTResolver;
use Symfony\Component\HttpFoundation\Response;
use Nette\Schema\Expect;

class EmailAuthRequest extends Request
{
    const EXPIRATION_TIME = '+4 hours';
    
    public function __construct(
        private readonly EntityManager   $em,
        private readonly JWTResolver     $jwt,
        private readonly ClaimsFormatter $claims,
        private readonly CrudHelper      $crudHelper
    )
    {
    }
    
    public function schema(): array
    {
        $all = $this->crudHelper->getAllEntities();
        $filtered = array_filter($all, fn($item) => is_subclass_of($item['value'], AuthUser::class));
        $tables = array_map(fn($class) => $class['table'], $filtered);
        
        return [
            'source' => Expect::anyOf(...$tables),
            'email' => Expect::email()->max(128)->required(),
            'password' => Expect::string()->min(6)->max(32)->required()
        ];
    }
    
    public function process(array $data): Response
    {
        $className = $this->crudHelper->getEntityClassName($data['source']);
        
        if (!$className || !is_subclass_of($className, AuthUser::class)) {
            return $this->error(['Invalid source']);
        }
        
        $userRepo = $this->em->getRepository($className);
        $roleRepo = $this->em->getAuthRoleRepo();
        $tokenRepo = $this->em->getAuthTokenRepo();
        //$resourceRepo = $this->em->getAuthResourceRepo();
        
        /** @var AuthUser|null $user */
        $user = $userRepo->findOneBy(['email' => $data['email']]);
        
        if (!$user || !(new Passwords(PASSWORD_ARGON2ID))->verify($data['password'], $user->getPassword())) {
            return $this->error(['Invalid credentials'], 401);
        }
        
        // Prevent multiple user login
        /** @var \Saas\Database\Entity\Auth\Token|null $token */
        //$token = $tokenRepo->findOneBy(['sourceId' => $user->getId(), 'source' => $data['source']]);
        
        //if (!$token) {
            $token = new Token();
            $token->setSource($data['source']);
            $token->setSourceId($user->getId());
            $this->em->persist($token);
        //}
        
        // TODO: user roles and theirs resources
        $resources = null;
        
        if ($className !== Admin::class) {
            /** @var \Saas\Database\Entity\Auth\Role[] $roles */
            $roles = $roleRepo->findBy([]);
            $resources = [];
        } else {
            $roles = [(new Role())->setName('admin')];
        }
        
        $expiration = (new \DateTime())->modify(self::EXPIRATION_TIME);
        $immutable = \DateTimeImmutable::createFromMutable($expiration);
        $claims = $this->claims->format($user, $roles, $resources);
        $jwt = $this->jwt->createToken($immutable, $claims);
        
        $token->setExpiration($expiration);
        $token->setToken($jwt);
        $user->setLastLogin();
        
        $this->em->flush($token);
        $this->em->flush($user);
        
        return $this->json([
            'bearer_token' => $token->getToken(),
            'token_id' => $token->getId(),
            ...$claims
        ]);
    }
}