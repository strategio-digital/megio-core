<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Extension\Latte\Extension;

use Latte\Extension;
use Saas\Extension\Latte\Function\RouteFunction;
use Saas\Extension\Latte\Function\ThumbnailFunction;
use Saas\Extension\Latte\Function\ViteFunction;

class SaasExtension extends Extension
{
    public function __construct(protected RouteFunction $routeFunction)
    {
    }
    
    /**
     * @return array<string, mixed>
     */
    public function getFunctions(): array
    {
        return  [
            'vite' => [ViteFunction::class, 'create'], // Static
            'thumbnail' => [ThumbnailFunction::class, 'create'], // Static
            'route' => [$this->routeFunction, 'create'], // With DI
        ];
    }
}