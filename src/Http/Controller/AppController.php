<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

use Nette\DI\Container;
use Saas\Helper\Path;
use Saas\Helper\Router;
use Saas\Http\Controller\Base\Controller;
use Saas\Storage\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class AppController extends Controller
{
    public function app(Container $container, string|int|float $uri = null): Response
    {
        /** @var \Symfony\Component\Routing\RouteCollection $routes */
        $routes = $container->getByName('routes');
        
        /** @var \Symfony\Component\Routing\Route $route */
        $route = $routes->get(Router::ROUTE_ADMIN);
        $appPath = $route->compile()->getStaticPrefix();
        
        return $this->render(Path::saasVendorDir() . '/view/controller/admin.latte', [
            'appPath' => $appPath,
        ]);
    }
    
    public function api(Storage $storage, Container $container): Response
    {
        /** @var \Symfony\Component\Routing\RouteCollection $routes */
        $routes = $container->getByName('routes');
        
        $prettyRoutes = array_map(fn(Route $route) => [
            'path' => $route->getPath(),
            'methods' => $route->getMethods(),
            'rules' => $route->getRequirements(),
        ], $routes->all());
        
        $dt = new \DateTime();
        
        return $this->json([
            'name' => $_ENV['APP_NAME'],
            'mode' => $_ENV['APP_ENV_MODE'],
            'storage_adapter' => $storage->getAdapterName(),
            'execution_time' => floor((microtime(true) - $container->parameters['startedAt']) * 1000) . 'ms',
            'current_dt' => [
                'date_time' => $dt->format('Y.m.d H:i:s:u'),
                'time_zone' => $dt->getTimezone()->getName()
            ],
            'endpoints' => [
                'count' => count($prettyRoutes),
                ...$prettyRoutes
            ]
        ]);
    }
}