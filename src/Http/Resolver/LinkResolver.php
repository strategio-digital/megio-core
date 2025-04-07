<?php
declare(strict_types=1);

namespace Megio\Http\Resolver;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkResolver
{
    public function __construct(protected UrlGenerator $urlGenerator) {}

    /**
     * @param string $name
     * @param array<string, string|int> $params
     * @param int $path
     * @return string
     */
    public function link(
        string $name,
        array $params = [],
        int $path = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        $link = $this->urlGenerator->generate(
            name: $name,
            parameters: $params,
            referenceType: $path,
        );

        if (php_sapi_name() == 'cli' && $path === UrlGeneratorInterface::ABSOLUTE_URL) {
            $appUrl = $_ENV['APP_URL'];
            if (str_ends_with($_ENV['APP_URL'], '/')) {
                $appUrl = substr($_ENV['APP_URL'], 0, -1);
            }

            return $appUrl . $link;
        }

        return $link;
    }
}