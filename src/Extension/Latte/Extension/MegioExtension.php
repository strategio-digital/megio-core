<?php
declare(strict_types=1);

namespace Megio\Extension\Latte\Extension;

use Latte\Extension;
use Megio\Extension\Latte\Function\RouteFunction;
use Megio\Extension\Latte\Function\ThumbnailFunction;
use Megio\Extension\Latte\Function\ViteFunction;

class MegioExtension extends Extension
{
    public function __construct(protected RouteFunction $routeFunction) {}

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
