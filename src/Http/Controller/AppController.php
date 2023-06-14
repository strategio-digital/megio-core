<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

use Nette\Utils\Strings;
use Saas\Helper\Path;
use Saas\Http\Router\Router;
use Saas\Http\Router\RouterFactory;
use Saas\Storage\Storage;
use Nette\DI\Container;

class AppController extends Controller
{
    public function app(Container $container, string|int|float $uri = null): void
    {
        /** @var RouterFactory $routeFactory */
        $routeFactory = $container->getByName('routerFactory');
        
        /** @var \Symfony\Component\Routing\Route $route */
        $route = $routeFactory->getRouteCollection()->get(Router::APP);
        $appPath = $route->compile()->getStaticPrefix();
        
        $this->getResponse()->render(Path::saasVendorDir() . '/view/controller/admin.latte', [
            'appPath' => $appPath,
        ]);
    }
    
    public function api(Storage $storage, Container $container): void
    {
        /** @var RouterFactory $routeFactory */
        $routeFactory = $container->getByName('routerFactory');
        
        /** @var \Symfony\Component\Routing\Route $route */
        $route = $routeFactory->getRouteCollection()->get(Router::API);
        $apiPath = $route->compile()->getStaticPrefix();
        
        $routes = [];
        foreach ($routeFactory->getRouteCollection()->all() as $key => $route) {
            $routes[Strings::startsWith($route->getPath(), $apiPath) ? Router::API : Router::APP][] = [
                'name' => $key,
                'method' => $route->getMethods()[0],
                'path' => $route->getPath()
            ];
        }
        
        $dt = new \DateTime();
        
        $this->getResponse()->send([
            'name' => $_ENV['APP_NAME'],
            'mode' => $_ENV['APP_ENV_MODE'],
            'storage_adapter' => $storage->getAdapterName(),
            'execution_time' => floor((microtime(true) - $container->parameters['startedAt']) * 1000) . 'ms',
            'current_dt' => [
                'date_time' => $dt->format('Y.m.d H:i:s:u'),
                'time_zone' => $dt->getTimezone()->getName()
            ],
            'endpoints' => [
                'count' => array_reduce($routes, fn($acc, $r) => $acc + count($r), 0),
                ...$routes
            ]
        ]);
    }
}