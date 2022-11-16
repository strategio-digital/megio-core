<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Router;

use Symfony\Component\Routing\Matcher\UrlMatcher;

class RouterFactory extends \Saas\Router\RouterFactory
{
    public function create(): UrlMatcher
    {
        //$this->add('GET', '/invoice/download', [InvoiceController::class, 'download']);
        
        return parent::create();
    }
}
