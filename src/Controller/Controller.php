<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Controller;

use Framework\Http\Request\Request;
use Framework\Http\Response\Response;

abstract class Controller implements IController
{
    public function __construct(
        private readonly Response $response,
        private readonly Request  $request
    )
    {
    }
    
    public function startup(): void
    {
    }
    
    /**
     * @return \Framework\Http\Response\Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
    
    /**
     * @return \Framework\Http\Request\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
