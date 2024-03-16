<?php
declare(strict_types=1);

namespace Megio\Event\Collection;

use Megio\Collection\RecipeEntityMetadata;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class OnProcessingStartEvent extends Event
{
    protected ?Response $response = null;
    
    public function __construct(
        private mixed                         $data,
        private readonly Request              $request,
        private readonly RecipeEntityMetadata $metadata,
    )
    {
    }
    
    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }
    
    /**
     * @return \Megio\Collection\RecipeEntityMetadata
     */
    public function getMetadata(): RecipeEntityMetadata
    {
        return $this->metadata;
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