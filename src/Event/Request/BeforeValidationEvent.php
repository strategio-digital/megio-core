<?php
declare(strict_types=1);

namespace Megio\Event\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeValidationEvent extends Event
{
    public function __construct(
        private mixed            $data,
        private mixed            $schema,
        private readonly Request $request
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