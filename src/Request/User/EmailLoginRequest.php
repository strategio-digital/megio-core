<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Request\User;

use Framework\Database\Entity\Role\Resource;
use Framework\Database\Entity\User\Token;
use Framework\Database\EntityManager;
use Framework\Http\Request\IRequest;
use Framework\Http\Response\Response;
use Framework\Security\JWT\Claims;
use Framework\Security\JWT\Jwt;
use Framework\Security\Permissions\DefaultRole;
use Nette\Schema\Expect;
use Nette\Security\Passwords;

class EmailLoginRequest implements IRequest
{
    public function __construct(
        private readonly EntityManager $em,
        private readonly Response      $response,
        private readonly Jwt           $jwt,
        private readonly Claims        $claims
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
        $userRepo = $this->em->getUserRepo();
        $userTokenRepo = $this->em->getUserTokenRepo();
        
        /** @var \Framework\Database\Entity\User\User|null $user */
        $user = $userRepo->findOneBy(['email' => $data['email']]);
        
        if (!$user || !(new Passwords(PASSWORD_ARGON2ID))->verify($data['password'], $user->getPassword())) {
            $this->response->sendError(['messages' => 'Invalid credentials'], 401);
        }
        
        /** @var \Framework\Database\Entity\User\Token|null $userToken */
        $userToken = $userTokenRepo->findOneBy(['user' => $user->getId()]);
        
        if (!$userToken) {
            $userToken = (new Token())->setUser($user);
            $this->em->persist($userToken);
        }
        
        $expiration = (new \DateTime())->modify('+4 hours');
        $immutable = \DateTimeImmutable::createFromMutable($expiration);
        
        $qb = $this->em->getRoleResourceRepo()->createQueryBuilder('Resource')
            ->addSelect('Resource')
            ->leftJoin('Resource.roles', 'Permissions');
        
        if ($user->getRole()->getName() !== DefaultRole::Admin->name()) {
            $qb->where('Permissions.name = :name')
                ->setParameter('name', $userToken->getUser()->getRole()->getName());
        }
        
        $claims = $this->claims->format($user, $qb->getQuery()->execute());
        $token = $this->jwt->createToken($immutable, $claims);
        
        $userToken
            ->setExpiration($expiration)
            ->setToken($token);
        
        $this->em->flush($userToken);
        
        $this->response->send(['bearer_token' => $userToken->getToken(), ...$claims]);
    }
}