<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Event\Collection;

use Megio\Database\CrudHelper\EntityMetadata;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class OnProcessingExceptionEvent extends Event
{
    public function __construct(
        private mixed                   $data,
        private readonly Request        $request,
        private readonly EntityMetadata $metadata,
        private readonly \Throwable     $exception,
        private Response                $response,
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
     * @return \Megio\Database\CrudHelper\EntityMetadata
     */
    public function getMetadata(): EntityMetadata
    {
        return $this->metadata;
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