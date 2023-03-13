<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Router;

use Saas\Http\Controller\Error404;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

abstract class Router
{
    private RouteCollection $routes;
    
    private int $routerCounter = 0;
    
    public function __construct(protected Request $request, protected RequestContext $requestContext)
    {
        $this->routes = new RouteCollection;
    }
    
    /**
     * @param string $method
     * @param string $path
     * @param array<int, string> $route
     * @param array<string, string|int> $params
     * @param string|null $name
     * @param array<string, string> $requirements
     * @return void
     */
    public function add(string $method, string $path, array $route, array $params = [], string $name = null, array $requirements = []): void
    {
        $route = array_merge(['_controller' => $route[0], '_action' => $route[1]], $params);
        $routeName = $name ?: 'auto-generated-name-' . $this->routerCounter++;
        $this->routes->add($routeName, new Route($path, $route, $requirements, [], '', [], $method));
    }
    
    /**
     * @return UrlMatcher
     */
    public function create(): UrlMatcher
    {
        return new UrlMatcher($this->routes, $this->requestContext);
    }
    
    /**
     * @param UrlMatcher $matcher
     * @return array<string, string>
     */
    public function matchAsController(UrlMatcher $matcher): array
    {
        try {
            $controllerData = $matcher->matchRequest($this->request);
        } catch (\Exception) {
            $controllerData = ['_controller' => Error404::class];
        }
        
        return $controllerData;
    }
    
    public function getRouteCollection(): RouteCollection
    {
        return $this->routes;
    }
}
