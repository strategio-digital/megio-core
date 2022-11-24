<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

use Saas\Http\Request\Request;
use Saas\Http\Response\Response;

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
     * @return \Saas\Http\Response\Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
    
    /**
     * @return \Saas\Http\Request\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
