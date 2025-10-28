<?php
declare(strict_types=1);

namespace Megio\Event\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeProcessEvent extends Event
{
    public function __construct(
        private mixed            $data,
        private mixed            $schema,
        private readonly Request $request,
    ) {}

    /**
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     */
    public function getSchema(): mixed
    {
        return $this->schema;
    }

    /**
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     */
    public function setSchema(mixed $schema): void
    {
        $this->schema = $schema;
    }
}
