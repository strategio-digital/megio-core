<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Controller;

use Framework\Http\Request\Request;
use Framework\Http\Response\Response;

interface IController
{
    public function startup(): void;
    
    public function getRequest(): Request;
    
    public function getResponse(): Response;
}