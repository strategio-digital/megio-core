<?php
declare(strict_types=1);

namespace Megio\Event\Collection;

use Megio\Collection\RecipeEntityMetadata;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class OnProcessingFinishEvent extends Event
{
    public function __construct(
        private mixed                         $data,
        private readonly Request              $request,
        private readonly RecipeEntityMetadata $metadata,
        private mixed                         $result,
        private Response                      $response,
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
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    /**
     * @return \Megio\Collection\RecipeEntityMetadata
     */
    public function getMetadata(): RecipeEntityMetadata
    {
        return $this->metadata;
    }
    
    /**
     * @return mixed
     */
    public function getResult(): mixed
    {
        return $this->result;
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
     * @param mixed $result
     */
    public function setResult(mixed $result): void
    {
        $this->result = $result;
    }
    
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}