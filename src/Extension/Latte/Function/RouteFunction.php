<?php
declare(strict_types=1);

namespace Megio\Extension\Latte\Function;

use Megio\Http\Resolver\LinkResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouteFunction
{
    public function __construct(protected LinkResolver $linkResolver) {}

    /**
     * @param array<string, int|string> $params
     */
    public function create(
        string $name,
        array $params = [],
        int $path = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        return $this->linkResolver->link($name, $params, $path);
    }
}
