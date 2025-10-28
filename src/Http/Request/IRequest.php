<?php
declare(strict_types=1);

namespace Megio\Http\Request;

use Symfony\Component\HttpFoundation\Response;

interface IRequest
{
    /**
     * @param array<int|string, mixed> $data
     *
     * @return array<int|string, mixed>
     */
    public function schema(array $data): array;

    /**
     * @param array<int|string, mixed> $data
     */
    public function process(array $data): Response;
}
