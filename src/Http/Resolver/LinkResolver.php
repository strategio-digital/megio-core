<?php
declare(strict_types=1);

namespace Megio\Http\Resolver;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkResolver
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