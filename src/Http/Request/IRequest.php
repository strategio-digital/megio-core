<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request;

use Symfony\Component\HttpFoundation\Response;

interface IRequest
{
    /**
     * @return array<string|int, mixed>
     */
    public function schema(): array;
    
    /**
     * @param array<string|int, mixed> $data
     * @return Response
     */
    public function process(array $data): Response;
}