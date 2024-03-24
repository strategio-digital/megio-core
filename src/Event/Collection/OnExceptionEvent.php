<?php
declare(strict_types=1);

namespace Megio\Event\Collection;

use Megio\Collection\ICollectionRecipe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class OnExceptionEvent extends Event
{
    public function __construct(
        protected EventType         $eventType,
        protected mixed             $data,
        protected ICollectionRecipe $recipe,
        protected \Throwable        $exception,
        protected Request           $request,
        private Response            $response,
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
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    /**
     * @return \Megio\Collection\ICollectionRecipe
     */
    public function getRecipe(): ICollectionRecipe
    {
        return $this->recipe;
    }
    
    /**
     * @return \Throwable
     */
    public function getException(): \Throwable
    {
        return $this->exception;
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
    
    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }
    
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}