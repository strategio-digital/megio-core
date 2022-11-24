<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Router;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkGenerator
{
    public function __construct(protected UrlGenerator $urlGenerator)
    {
    }
    
    /**
     * @param string $name
     * @param array<string, string|int> $params
     * @param int $path
     * @return string
     */
    public function link(string $name, array $params = [], int $path = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->urlGenerator->generate($name, $params, $path);
    }
}