<?php
declare(strict_types=1);

namespace Megio\Http\Controller;

use Nette\DI\Container;
use Nette\Utils\Strings;
use Megio\Helper\Path;
use Megio\Helper\Router;
use Megio\Http\Controller\Base\Controller;
use Megio\Storage\Storage;
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
        
        return $this->render(Path::megioVendorDir() . '/view/controller/admin.latte', [
            'appPath' => $appPath,
            'appVersions' => $this->versions(),
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
                'route_rules' => count($route->getRequirements()) ? $route->getRequirements() : null
            ];
            
        }, $routes->all());
        
        $dt = new \DateTime();
        
        return $this->json([
            'name' => $_ENV['APP_NAME'],
            'mode' => $_ENV['APP_ENVIRONMENT'],
            'storage_adapter' => $storage->getAdapterName(),
            'megio_versions' => $this->versions(),
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
            $composer = current(array_filter($json['packages'], fn($package) => $package['name'] === 'strategio/megio-core'));
            
            if ($composer) {
                $composerVersion = $composer['version'];
                $commit = $composer['source']['reference'];
            }
        }
        
        $content = file_get_contents(Path::appDir() . '/../yarn.lock');
        
        if ($content) {
            $json = YarnLock::toArray($content);
            $yarn = current(array_filter($json, fn(string $key) => Strings::startsWith($key, 'megio-panel@'), ARRAY_FILTER_USE_KEY));
            
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