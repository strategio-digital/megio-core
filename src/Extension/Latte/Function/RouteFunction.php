<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Extension\Latte\Function;

use Megio\Http\Resolver\LinkResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouteFunction
{
    public function __construct(protected LinkResolver $linkResolver)
    {
    }
    
    /**
     * @param string $name
     * @param array<string, int|string> $params
     * @param int $path
     * @return string
     */
    public function create(string $name, array $params = [], int $path = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->linkResolver->link($name, $params, $path);
    }
}