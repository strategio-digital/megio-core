<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Http\Router;

use App\Http\Controller\HomeController;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class RouterFactory extends \Saas\Http\Router\RouterFactory
{
    public function create(): UrlMatcher
    {
        // Homepage
        $this->add('GET', '/', [HomeController::class, 'index'], [], 'home');
        
        return parent::create();
    }
}
