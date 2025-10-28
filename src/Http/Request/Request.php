<?php
declare(strict_types=1);

namespace Megio\Http\Request;

use Megio\Event\Request\AfterProcessEvent;
use Megio\Event\Request\BeforeProcessEvent;
use Megio\Event\Request\BeforeValidationEvent;
use Megio\Event\Request\Events;
use Megio\Event\Request\OnValidationExceptionEvent;
use Megio\Http\Controller\Base\Controller;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

abstract class Request extends Controller implements IRequest
{
    protected SymfonyRequest $request;

    /**
     * @param array<string, mixed>|null $data
     * @param array<int|string, mixed> $structure
     *
     * @return array<string, mixed>|false
     */
    public function validate(
        ?array $data,
        array $structure,
    ): array|false {
        $schema = Expect::structure($structure)->castTo('array');
        return (new Processor())->process($schema, $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequestData(): array
    {
        $raw = $this->request->getContent();
        $json = is_string($raw) ? json_decode($raw, true) : [];
        return array_merge($json ?: [], $this->request->files->all());
    }

    public function __invoke(SymfonyRequest $request): Response
    {
        $this->request = $request;

        $data = $this->getRequestData();
        $schema = $this->schema($data);

        if (count($schema) !== 0) {
            try {
                $event = new BeforeValidationEvent($data, $schema, $this->request);
                $this->dispatcher->dispatch($event, Events::BEFORE_VALIDATION->value);
                $vData = $this->validate($data, $schema);
                $data = $vData === false ? [] : $vData;

            } catch (ValidationException $exception) {
                $event = new OnValidationExceptionEvent($data, $schema, $this->request, $exception);
                $this->dispatcher->dispatch($event, Events::ON_VALIDATION_EXCEPTION->value);

                return $this->error($exception->getMessages());
            }
        }

        $event = new BeforeProcessEvent($data, $schema, $this->request);
        $this->dispatcher->dispatch($event, Events::BEFORE_PROCESSING_DATA->value);

        $response = $this->process($data);

        $event = new AfterProcessEvent($data, $schema, $response);
        $this->dispatcher->dispatch($event, Events::AFTER_PROCESSING_DATA->value);

        return $response;
    }
}
