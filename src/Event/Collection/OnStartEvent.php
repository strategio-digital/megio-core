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
        protected EventType         $eventType,
        protected mixed             $data,
        protected ICollectionRecipe $recipe,
        protected Request           $request,
    )
    {
    }
    
    public function getEventType(): EventType
    {
        return $this->eventType;
    }
    
    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }
    
    /**
     * @return ICollectionRecipe
     */
    public function getRecipe(): ICollectionRecipe
    {
        return $this->recipe;
    }
    
    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }
    
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}