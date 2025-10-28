<?php
declare(strict_types=1);

namespace Megio\Event\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class AfterProcessEvent extends Event
{
    public function __construct(
        private mixed $schema,
        private mixed $data,
        private Response $response,
    ) {}

    /**
     */
    public function getSchema(): mixed
    {
        return $this->schema;
    }

    /**
     */
    public function setSchema(mixed $schema): void
    {
        $this->schema = $schema;
    }

    /**
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
