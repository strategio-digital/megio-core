<?php
declare(strict_types=1);

namespace Megio\Subscriber;

use Megio\Database\Entity\Admin;
use Megio\Event\Collection\CollectionEvent;
use Megio\Event\Collection\OnProcessingStartEvent;
use Megio\Security\Auth\AuthUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        
        $recipeName = $event->getMetadata()->getRecipe()->name();
        $resourceName = $routeName . '.' . $recipeName;
        
        if (!in_array($resourceName, $this->authUser->getResources())) {
            $message = "This collection-resource '{$resourceName}' is not allowed for current user";
            $this->event->setResponse(new JsonResponse(['errors' => [$message]], 401));
        }
    }
}