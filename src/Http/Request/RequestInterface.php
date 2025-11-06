<?php
declare(strict_types=1);

namespace Megio\Http\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RequestInterface
{
    public function process(Request $request): Response;
}
