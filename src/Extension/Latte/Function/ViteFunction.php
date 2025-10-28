<?php
declare(strict_types=1);

namespace Megio\Extension\Latte\Function;

use Megio\Extension\Latte\Helper\Vite;

class ViteFunction
{
    protected static ?Vite $vite = null;

    public static function create(
        string $source,
        bool $isEntryPoint = false,
    ): string {
        if (!self::$vite) {
            self::$vite = new Vite();
        }

        return $isEntryPoint ? self::$vite->resolveEntrypoint($source) : self::$vite->resolveSource($source);
    }
}
