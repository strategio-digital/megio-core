<?php
declare(strict_types=1);

namespace Megio\Debugger;

use Doctrine\DBAL\Logging\DebugStack;
use Nette\DI\Container;
use Megio\Extension\Doctrine\Tracy\SummaryHelper;
use Megio\Security\Auth\AuthUser;

class ResponseFormatter
{
    public function __construct(
        private readonly Container $container,
        private readonly AuthUser $user,
        private readonly DebugStack $queryStack,
    )
    {
    }
    
    /**
     * @param array<string|int, mixed> $data
     * @return array<string|int, mixed>
     */
    public function formatResponseData(array $data): array
    {
        $user = $this->user->get();
        $queriesHelper = new SummaryHelper($this->queryStack);
        
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
                    'queries' => $queriesHelper->count() ? array_values($this->queryStack->queries) : []
                ]
            ]
        ]);
    }
}