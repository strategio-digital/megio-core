<?php
declare(strict_types=1);

namespace Megio\Event\Collection;

use Megio\Collection\ICollectionRecipe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class OnStartEvent extends Event
{
    protected ?Response $response = null;

    public function __construct(
        protected EventType $eventType,
        protected mixed $data,
        protected ICollectionRecipe $recipe,
        protected Request $request,
    ) {}

    public function getEventType(): EventType
    {
        return $this->eventType;
    }

    /**
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     */
    public function getRecipe(): ICollectionRecipe
    {
        return $this->recipe;
    }

    /**
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
