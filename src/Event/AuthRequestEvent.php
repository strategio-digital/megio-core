<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Event;

use Saas\Database\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouteCollection;

class AuthRequestEvent implements EventSubscriberInterface
{
    public function __construct(protected RouteCollection $routes, protected EntityManager $em)
    {
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelHandle'],
        ];
    }
    
    public function onKernelHandle(ControllerEvent $event): void
    {
//        $routeName = $event->getRequest()->attributes->get('_route');
//        $route = $this->routes->get($routeName);
    }
}