<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Extension\Latte\Function;

use Saas\Extension\Latte\Helper\Thumbnail;

class ThumbnailFunction
{
    public static function create(string $path, ?int $width, ?int $height, string $method = 'EXACT', int $quality = 80): Thumbnail
    {
        return new Thumbnail($path, $width, $height, $method, $quality);
    }
}