<?php
declare(strict_types=1);

namespace Megio\Debugger;

use Megio\Extension\Doctrine\Middleware\QueryLogger;
use Megio\Extension\Doctrine\Tracy\SummaryHelper;
use Megio\Security\Auth\AuthUser;
use Nette\DI\Container;

readonly class ResponseFormatter
{
    public function __construct(
        private Container $container,
        private AuthUser $user,
        private QueryLogger $queryLogger,
    ) {}

    /**
     * @param array<int|string, mixed> $data
     *
     * @return array<int|string, mixed>
     */
    public function formatResponseData(array $data): array
    {
        $user = $this->user->get();
        $queriesHelper = new SummaryHelper($this->queryLogger);

        $executionTime = microtime(true) - $this->container->parameters['startedAt'];

        return $_ENV['APP_ENVIRONMENT'] !== 'develop' ? $data : array_merge($data, [
            '@debug' => [
                'execution_time' => number_format($executionTime * 1000, 1, '.') . 'ms',
                'auth_user' => $user ? [
                    'id' => $user->getId(),
                    'roles' => $this->user->getRoles(),
                    'resources_count' => count($this->user->getResources()),
                ] : null,
                'database' => [
                    'query_time' => number_format($queriesHelper->getTotalTime() * 1000, 1, '.') . 'ms',
                    'query_count' => $queriesHelper->count(),
                    'queries' => $queriesHelper->count() ? array_values($this->queryLogger->queries) : [],
                ],
            ],
        ]);
    }
}
