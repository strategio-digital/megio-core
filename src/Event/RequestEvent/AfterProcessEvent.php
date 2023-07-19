<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Event\RequestEvent;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class AfterProcessEvent extends Event
{
    public function __construct(
        private mixed    $schema,
        private mixed    $data,
        private Response $response,
    )
    {
    }
    
    /**
     * @return mixed
     */
    public function getSchema(): mixed
    {
        return $this->schema;
    }
    
    /**
     * @param mixed $schema
     */
    public function setSchema(mixed $schema): void
    {
        $this->schema = $schema;
    }
    
    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }
    
    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(): Response
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