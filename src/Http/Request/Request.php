<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Http\Request;

use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request
{
    public function __construct(protected SymfonyRequest $request)
    {
    }
    
    public function getHttpRequest(): SymfonyRequest
    {
        return $this->request;
    }
    
    /**
     * @param array<string, mixed>|null $data
     * @param array<string|int, mixed> $structure
     * @return array<string, mixed>|false
     */
    public function validate(array|null $data, array $structure): array|false
    {
        $schema = Expect::structure($structure);
        $data = (new Processor())->process($schema, $data);
        return json_decode((string)json_encode($data), true);
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
}