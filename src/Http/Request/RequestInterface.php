<?php
declare(strict_types=1);

namespace Megio\Http\Request;

use Megio\Http\Serializer\RequestSerializerException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RequestInterface
{
    /**
     * @throws RequestSerializerException
     */
    public function process(Request $request): Response;
}
