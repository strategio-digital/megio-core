<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Extension\Latte\Function;

use Saas\Extension\Helper\Vite;

class ViteFunction
{
    protected static ?Vite $vite = null;
    
    public static function create(string $source, bool $isEntryPoint = false): string
    {
        if (!self::$vite) {
            self::$vite = new Vite();
        }
        
        return $isEntryPoint ? self::$vite->resolveEntrypoint($source) : self::$vite->resolveSource($source);
    }
}