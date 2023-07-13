<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request;

use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Saas\Http\Controller\Base\Controller;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

abstract class Request extends Controller implements IRequest
{
    private SymfonyRequest $request;
    
    /**
     * @param array<string, mixed>|null $data
     * @param array<string|int, mixed> $structure
     * @return array<string, mixed>|false
     */
    public function validate(array|null $data, array $structure): array|false
    {
        $schema = Expect::structure($structure)->castTo('array');
        return (new Processor())->process($schema, $data);
    }
    
    /**
     * @return array<string, mixed>
     */
    public function getRequestData(): array
    {
        $raw = $this->request->getContent();
        $json = is_string($raw) ? json_decode($raw, true) : []; //@phpstan-ignore-line
        return array_merge($json ?: [], $this->request->files->all());
    }
    
    public function __invoke(SymfonyRequest $request): Response
    {
        $this->request = $request;
        
        $data = $this->getRequestData();
        $schema = $this->schema();
        
        if (count($schema) === 0) {
            return $this->process($data);
        }
        
        try {
            $vData = $this->validate($data, $schema);
            $data = $vData === false ? [] : $vData;
        } catch (ValidationException $exception) {
            return $this->error($exception->getMessages());
        }
        
        return $this->process($data);
    }
}