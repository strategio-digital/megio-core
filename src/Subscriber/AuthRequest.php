<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Subscriber;

use Saas\Database\CrudHelper\CrudHelper;
use Saas\Database\Entity\Admin;
use Saas\Database\EntityManager;
use Saas\Database\Interface\IAuthenticable;
use Saas\Security\Auth\AuthUser;
use Saas\Security\JWT\JWTResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouteCollection;

class AuthRequest implements EventSubscriberInterface
{
    protected RequestEvent $event;
    
    protected Request $request;
    
    public function __construct(
        protected CrudHelper      $crudHelper,
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
        
        /** @var \Saas\Database\Entity\Auth\Token|null $token */
        $token = $this->em->getAuthTokenRepo()->findOneBy(['id' => $tokenId]);
        
        if (!$token) {
            $this->sendError('Unknown JWT token, probably it was revoked');
            return;
        }
        
        /** @var \Saas\Database\Entity\Auth\Token $token */
        if ($jwt->isExpired(new \DateTime())) {
            $this->em->remove($token);
            $this->em->flush();
            $this->sendError('JWT token expired');
            return;
        }
        
        $className = $this->crudHelper->getEntityClassName($token->getSource());
        
        if (!$className || !is_subclass_of($className, IAuthenticable::class)) {
            $this->sendError("For source {$token->getSource()} does not exists IAuthenticable entity");
            return;
        }
        
        /** @var class-string $className */
        $userRepo = $this->em->getRepository($className);
        
        $qb = $userRepo->createQueryBuilder('User')
            ->addSelect('User')
            ->andWhere('User.id = :source_id')
            ->setParameter('source_id', $token->getSourceId());
        
        if ($className !== Admin::class) {
            $qb
                ->addSelect('Role')
                ->addSelect('Resource')
                ->leftJoin('User.roles', 'Role')
                ->leftJoin('Role.resources', 'Resource');
        }
        
        $user = $qb->getQuery()->getOneOrNullResult();
        
        if (!$user) {
            $this->sendError("User does not exist in '{$token->getSource()}' source");
            return;
        }
        
        /** @var IAuthenticable $user */
        $this->authUser->setAuthUser($user);
        
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