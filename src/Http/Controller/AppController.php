<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

use Nette\DI\Container;
use Nette\Utils\Strings;
use Saas\Helper\Path;
use Saas\Helper\Router;
use Saas\Http\Controller\Base\Controller;
use Saas\Storage\Storage;
use Siketyan\YarnLock\YarnLock;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class AppController extends Controller
{
    public function app(Container $container, string|int|float $uri = null): Response
    {
        /** @var \Symfony\Component\Routing\RouteCollection $routes */
        $routes = $container->getByName('routes');
        
        /** @var \Symfony\Component\Routing\Route $route */
        $route = $routes->get(Router::ROUTE_APP);
        $appPath = $route->compile()->getStaticPrefix();
        
        return $this->render(Path::saasVendorDir() . '/view/controller/admin.latte', [
            'appPath' => $appPath,
            'saasVersions' => $this->versions(),
        ]);
    }
    
    public function api(Storage $storage, Container $container): Response
    {
        /** @var \Symfony\Component\Routing\RouteCollection $routes */
        $routes = $container->getByName('routes');
        
        $prettyRoutes = array_map(function (Route $route) {
            $options = array_filter($route->getOptions(), fn($key) => $key !== 'compiler_class', ARRAY_FILTER_USE_KEY);
            
            return [
                'path' => $route->getPath(),
                'methods' => count($route->getMethods()) ? $route->getMethods() : null,
                'options' => count($options) ? $options : null,
                'route_rules' => count($route->getRequirements()) ? $route->getRequirements() : null,
                'schema_rules' => '@not-implemented-yet'
            ];
            
        }, $routes->all());
        
        $dt = new \DateTime();
        
        return $this->json([
            'name' => $_ENV['APP_NAME'],
            'mode' => $_ENV['APP_ENV_MODE'],
            'log_adapter' => $_ENV['LOG_ADAPTER'],
            'storage_adapter' => $storage->getAdapterName(),
            'saas_versions' => $this->versions(),
            'current_dt' => [
                'date_time' => $dt->format('Y.m.d H:i:s:u'),
                'time_zone' => $dt->getTimezone()->getName()
            ],
            'execution_time' => floor((microtime(true) - $container->parameters['startedAt']) * 1000) . 'ms',
            'endpoints' => [
                'count' => $routes->count(),
                'items' => $prettyRoutes
            ]
        ]);
    }
    
    /**
     * @return array{ composer: string|null, yarn: string|null }
     */
    private function versions(): array
    {
        $composerVersion = null;
        $yarnVersion = null;
        $commit = null;
        
        $content = file_get_contents(Path::appDir() . '/../composer.lock');
        
        if ($content && $json = json_decode($content, true)) {
            $composer = current(array_filter($json['packages'], fn($package) => $package['name'] === 'strategio/saas'));
            
            if ($composer) {
                $composerVersion = $composer['version'];
                $commit = $composer['source']['reference'];
            }
        }
        
        $content = file_get_contents(Path::appDir() . '/../yarn.lock');
        
        if ($content) {
            $json = YarnLock::toArray($content);
            $yarn = current(array_filter($json, fn($key) => Strings::startsWith($key, 'strategio-saas@'), ARRAY_FILTER_USE_KEY));
            
            if ($yarn) {
                $yarnVersion = $yarn['version'];
            }
        }
        
        return [
            'yarn' => $yarnVersion,
            'composer' => $composerVersion,
            'commit_reference' => $commit
        ];
    }
}