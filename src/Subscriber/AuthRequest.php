<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Subscriber;

use Megio\Database\EntityFinder;
use Megio\Database\Entity\Admin;
use Megio\Database\EntityManager;
use Megio\Database\Interface\IAuthenticable;
use Megio\Security\Auth\AuthUser;
use Megio\Security\JWT\JWTResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouteCollection;

class AuthRequest implements EventSubscriberInterface
{
    protected RequestEvent $event;
    
    protected Request $request;
    
    public function __construct(
        protected EntityFinder    $entityFinder,
        protected JWTResolver     $jwt,
        protected EntityManager   $em,
        protected RouteCollection $routes,
        protected AuthUser        $authUser,
    )
    {
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest'],
        ];
    }
    
    public function onRequest(RequestEvent $event): void
    {
        $this->event = $event;
        $this->request = $event->getRequest();
        
        $routeName = $this->request->attributes->get('_route');
        
        /** @var \Symfony\Component\Routing\Route $currentRoute */
        $currentRoute = $this->routes->get($routeName);
        
        if ($currentRoute->getOption('auth') === false) {
            return;
        }
        
        $authHeader = $this->request->headers->get('Authorization');
        
        if (!is_string($authHeader)) {
            $this->sendError('Invalid or empty Authorization header');
            return;
        }
        
        $bearer = trim(str_replace('Bearer', '', $authHeader));
        
        if (!$this->jwt->isTrustedToken($bearer)) {
            $this->sendError('Invalid or expired token');
            return;
        }
        
        $jwt = $this->jwt->parseToken($bearer);
        
        // @phpstan-ignore-next-line
        $claims = $jwt->claims();
        
        $tokenId = $claims->get('bearer_token_id');
        
        if (!$tokenId) {
            $this->sendError('Missing bearer_token_id in JWT token claims');
            return;
        }
        
        /** @var \Megio\Database\Entity\Auth\Token|null $token */
        $token = $this->em->getAuthTokenRepo()->findOneBy(['id' => $tokenId]);
        
        if (!$token) {
            $this->sendError('Unknown JWT token, probably it was revoked');
            return;
        }
        
        /** @var \Megio\Database\Entity\Auth\Token $token */
        if ($jwt->isExpired(new \DateTime())) {
            $this->em->remove($token);
            $this->em->flush();
            $this->sendError('JWT token expired');
            return;
        }
        
        $className = $this->entityFinder->getClassName($token->getSource());
        
        if (!$className || !is_subclass_of($className, IAuthenticable::class)) {
            $this->sendError("For source {$token->getSource()} does not exists IAuthenticable entity");
            return;
        }
        
        $userRepo = $this->em->getRepository($className);
        
        $qb = $userRepo->createQueryBuilder('user')
            ->select('user')
            ->andWhere('user.id = :source_id')
            ->setParameter('source_id', $token->getSourceId());
        
        if ($className !== Admin::class) {
            $qb
                ->addSelect('role')
                ->addSelect('resource')
                ->leftJoin('user.roles', 'role')
                ->leftJoin('role.resources', 'resource');
        }
        
        $user = $qb->getQuery()->getOneOrNullResult();
        
        if (!$user) {
            $this->sendError("User does not exist in '{$token->getSource()}' source");
            return;
        }
        
        /** @var IAuthenticable $user */
        $this->authUser->setAuthUser($user);
        
        // Don't watch only if strict mode is off
        $watch = !(array_key_exists('AUTH_STRICT_RESOURCES', $_ENV) && $_ENV['AUTH_STRICT_RESOURCES'] === 'false');
        
        if ($watch) {
            $claimsResources = $claims->get('user')['resources'];
            $authResources = $this->authUser->getResources();
            
            sort($claimsResources);
            sort($authResources);
            
            $requestResources = implode('|', $claimsResources);
            $userResources = implode('|', $authResources);
            
            if ($userResources !== $requestResources) {
                $this->sendError('User permissions have been changed');
            }
        }
    }
    
    /**
     * @param string $error
     * @param int $status
     */
    public function sendError(string $error, int $status = 401): void
    {
        $this->event->setResponse(new JsonResponse(['errors' => [$error]], $status));
        $this->event->stopPropagation();
    }
}