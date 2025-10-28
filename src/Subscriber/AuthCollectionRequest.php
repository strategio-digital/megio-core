<?php
declare(strict_types=1);

namespace Megio\Subscriber;

use Megio\Database\Entity\Admin;
use Megio\Event\Collection\Events;
use Megio\Event\Collection\OnStartEvent;
use Megio\Security\Auth\AuthUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AuthCollectionRequest implements EventSubscriberInterface
{
    protected OnStartEvent $event;

    protected Request $request;

    public function __construct(
        protected RouteCollection $routes,
        protected AuthUser        $authUser,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            Events::ON_START->value => ['onStart'],
        ];
    }

    public function onStart(OnStartEvent $event): void
    {
        $this->event = $event;
        $this->request = $event->getRequest();

        $routeName = $this->request->attributes->get('_route');

        if ($routeName === null) {
            return;
        }

        /** @var Route $currentRoute */
        $currentRoute = $this->routes->get($routeName);

        if ($currentRoute->getOption('auth') === false) {
            return;
        }

        if ($this->authUser->get() instanceof Admin) {
            return;
        }

        $recipeKey = $event->getRecipe()->key();
        $resourceName = $routeName . '.' . $recipeKey;

        if (!in_array($resourceName, $this->authUser->getResources(), true)) {
            $message = "Collection-resource '{$resourceName}' is not allowed for current user";
            $this->event->setResponse(new JsonResponse(['errors' => [$message]], 401));
        }
    }
}
