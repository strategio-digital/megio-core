<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Subscriber;

use Saas\Database\Entity\Admin;
use Saas\Event\Collection\CollectionEvent;
use Saas\Event\Collection\OnProcessingStartEvent;
use Saas\Security\Auth\AuthUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

class AuthCollectionRequest implements EventSubscriberInterface
{
    protected OnProcessingStartEvent $event;
    
    protected Request $request;
    
    public function __construct(
        protected RouteCollection $routes,
        protected AuthUser        $authUser
    )
    {
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            CollectionEvent::ON_PROCESSING_START => ['onProcess'],
        ];
    }
    
    public function onProcess(OnProcessingStartEvent $event): void
    {
        $this->event = $event;
        $this->request = $event->getRequest();
        
        $routeName = $this->request->attributes->get('_route');
        
        /** @var \Symfony\Component\Routing\Route $currentRoute */
        $currentRoute = $this->routes->get($routeName);
        
        if ($currentRoute->getOption('auth') === false) {
            return;
        }
        
        if ($this->authUser->get() instanceof Admin) {
            return;
        }
        
        $tableName = $event->getMetadata()->tableName;
        $resourceName = $routeName . '.' . $tableName;
        
        if (!in_array($resourceName, $this->authUser->getResources())) {
            $message = "This collection-resource '{$resourceName}' is not allowed for current user";
            $this->event->setResponse(new JsonResponse(['errors' => [$message]], 401));
        }
    }
}