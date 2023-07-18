<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Event;

use Saas\Database\Entity\Admin;
use Saas\Security\Auth\AuthUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouteCollection;

class AuthRouteRequestEvent implements EventSubscriberInterface
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
        
        $isAdmin = $this->authUser->get() instanceof Admin;
        
        if (!$isAdmin && !in_array($routeName, $this->authUser->getResources())) {
            $this->sendErrors(["This route-resource '{$routeName}' is not allowed for current user"]);
        }
    }
    
    /**
     * @param string[] $errors
     * @return void
     */
    public function sendErrors(array $errors): void
    {
        $this->event->setResponse(new JsonResponse(['errors' => $errors], 401));
        $this->event->getResponse()?->send();
    }
}