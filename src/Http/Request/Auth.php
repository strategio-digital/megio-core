<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request;

use Saas\Database\Entity\User\User;
use Saas\Database\EntityManager;
use Saas\Http\Response\Response;
use Saas\Security\JWT\Jwt;
use Saas\Security\Permissions\DefaultRole;
use Lcobucci\JWT\Token;

class Auth
{
    private ?Token $token = null;
    
    private ?User $user = null;
    
    private ?string $authHeader;
    
    public function __construct(
        private readonly Jwt           $jwt,
        private readonly EntityManager $em,
        private readonly Request       $request,
        private readonly Response      $response
    )
    {
        $this->authHeader = $this->request->getHttpRequest()->headers->get('Authorization');
    }
    
    private function invoke(): void
    {
        if (!is_string($this->authHeader)) {
            $this->response->sendError(['Invalid or empty Authorization header'], 401);
        }
        
        $tokenString = trim(str_replace('Bearer', '', $this->authHeader));
        
        if (!$this->jwt->isTrustedToken($tokenString)) {
            $this->response->sendError(['Invalid or expired token'], 401);
        }
        
        $token = $this->jwt->parseToken($tokenString);
        
        //@phpstan-ignore-next-line
        $userId = $token->claims()->get('user_id');
        
        if (!$userId) {
            $this->response->sendError(['Missing user_id claims in JWT token'], 401);
        }
        
        $repo = $this->em->getUserTokenRepo();
        
        /** @var \Saas\Database\Entity\User\Token|null $userToken */
        $userToken = $repo->createQueryBuilder('UserToken')
            ->addSelect('UserToken')
            ->addSelect('User')
            ->innerJoin('UserToken.user', 'User')
            ->where('UserToken.user = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getOneOrNullResult();
        
        if (!$userToken) {
            $this->response->sendError(['Unknown JWT token, probably it was revoked'], 401);
        }
        
        if ($userToken->getToken() !== $tokenString) {
            $this->response->sendError(['Different JWT token, probably you are logged-in on another device'], 401);
        }
        
        if ($token->isExpired(new \DateTime())) {
            $this->em->remove($userToken);
            $this->em->flush();
            $this->response->sendError(['JWT token expired.'], 401);
        }
        
        $this->token = $token;
        $this->user = $userToken->getUser();
    }
    
    public function getToken(): Token
    {
        if (!$this->token) {
            $this->invoke();
        }
        
        if (!$this->token) {
            throw new \Exception('You have to use #[ResourceGuard([\'...\']) before invoking Auth->getToken()');
        }
        
        return $this->token;
    }
    
    public function getUser(): User
    {
        if (!$this->user) {
            $this->invoke();
        }
        
        if (!$this->user) {
            throw new \Exception('You have to use #[ResourceGuard([\'...\']) before invoking Auth->getUser()');
        }
        
        return $this->user;
    }
    
    public function getRoleName(): string
    {
        if ($this->authHeader) {
            $this->invoke();
        }
        
        return $this->user ? $this->getUser()->getRole()->getName() : DefaultRole::Guest->name();
    }
}