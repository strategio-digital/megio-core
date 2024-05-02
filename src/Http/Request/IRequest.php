<?php
declare(strict_types=1);

namespace Megio\Http\Request;

use Symfony\Component\HttpFoundation\Response;

interface IRequest
{
    /**
     * @param array<string|int, mixed> $data
     * @return array<string|int, mixed>
     */
    public function schema(array $data): array;
    
    /**
     * @param array<string|int, mixed> $data
     * @return Response
     */
    public function process(array $data): Response;
}