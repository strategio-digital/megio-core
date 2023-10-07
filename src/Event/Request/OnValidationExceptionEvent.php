<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Event\Request;

use Nette\Schema\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class OnValidationExceptionEvent extends Event
{
    public function __construct(
        private mixed                        $data,
        private mixed                        $schema,
        private readonly Request             $request,
        private readonly ValidationException $exception
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
     * @return mixed
     */
    public function getSchema(): mixed
    {
        return $this->schema;
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    /**
     * @return \Nette\Schema\ValidationException
     */
    public function getException(): ValidationException
    {
        return $this->exception;
    }
    
    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }
    
    /**
     * @param mixed $schema
     */
    public function setSchema(mixed $schema): void
    {
        $this->schema = $schema;
    }
}