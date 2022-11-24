<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

class Error404 extends Controller
{
    public function index(): void
    {
        $this->getResponse()->sendError(['404 Not found'], 404);
    }
}