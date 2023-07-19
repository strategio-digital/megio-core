<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Event\CollectionEvent;

use Saas\Database\CrudHelper\EntityMetadata;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class OnProcessingStartEvent extends Event
{
    public function __construct(
        private mixed                   $data,
        private readonly Request        $request,
        private readonly EntityMetadata $metadata,
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
     * @return \Saas\Database\CrudHelper\EntityMetadata
     */
    public function getMetadata(): EntityMetadata
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
}