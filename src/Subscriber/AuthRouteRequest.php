<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Subscriber;

use Saas\Database\Entity\Admin;
use Saas\Security\Auth\AuthUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouteCollection;

class AuthRouteRequest implements EventSubscriberInterface
{
    protected RequestEvent $event;
    
    protected Request $request;
    
    public function __construct(protected RouteCollection $routes, protected AuthUser $authUser)
    {
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest'],
        ];
    }
    
    public function onRequest(RequestEvent $event): ?Response
    {
        $this->event = $event;
        $this->request = $event->getRequest();
        
        $routeName = $this->request->attributes->get('_route');
        
        /** @var \Symfony\Component\Routing\Route $currentRoute */
        $currentRoute = $this->routes->get($routeName);
        
        if ($currentRoute->getOption('auth') === false) {
            return null;
        }
        
        if ($this->authUser->get() instanceof Admin) {
            return null;
        }
        
        if (!in_array($routeName, $this->authUser->getResources())) {
            $message = "This route-resource '{$routeName}' is not allowed for current user";
            $response = new JsonResponse(['errors' => [$message]], 401);
            $this->event->setResponse($response);
            return $this->event->getResponse()?->send();
        }
        
        return null;
    }
}