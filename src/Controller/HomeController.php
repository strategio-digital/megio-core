<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Controller;

use Saas\Storage\Storage;
use Nette\DI\Container;

class HomeController extends Controller
{
    public function index(Storage $storage, Container $container): void
    {
        $dt = new \DateTime();
        $executionTime = microtime(true) - $container->parameters['startedAt'];
        
        $this->getResponse()->send([
            'name' => $_ENV['APP_NAME'],
            'mode' => $_ENV['APP_ENV_MODE'],
            'storage_adapter' => $storage->getAdapterName(),
            'execution_time' => floor($executionTime * 1000) . 'ms',
            'current_dt' => [
                'date_time' => $dt->format('Y.m.d H:i:s:u'),
                'time_zone' => $dt->getTimezone()->getName()
            ]
        ]);
    }
}