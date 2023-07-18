<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Debugger;

use Nette\DI\Container;
use Saas\Security\Auth\AuthUser;

class ResponseFormatter
{
    public function __construct(private readonly Container $container, private readonly AuthUser $user)
    {
    }
    
    /**
     * @param array<string|int, mixed> $data
     * @return array<string|int, mixed>
     */
    public function formatResponseData(array $data): array
    {
        $user = $this->user->get();
        $executionTime = microtime(true) - $this->container->parameters['startedAt'];
        
        return $_ENV['APP_ENV_MODE'] !== 'develop' ? $data : array_merge($data, [
            '#' => [
                'auth_user' => $user ? [
                    'id' => $user->getId(),
                    'roles' => $this->user->getRoles(),
                    'resources_count' => count($this->user->getResources()),
                ] : null,
                'execution_time' => floor($executionTime * 1000) . 'ms',
            ]
        ]);
    }
}