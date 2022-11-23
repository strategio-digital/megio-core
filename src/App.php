<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas;

use Saas\Controller\Controller;
use Saas\Guard\ResourceResolver;
use Saas\Guard\Attribute\ResourceGuard;
use Saas\Http\Request\IRequest;
use Saas\Router\Router;
use Nette\DI\Container;
use Nette\Schema\ValidationException;

class App
{
    public function run(Container $container): never
    {
        /** @var Router $router */
        $router = $container->getByName('routerFactory');
        $controllerData = $router->matchAsController($router->create());
        
        // Get controller data
        $actionName = $controllerData['_action'] ?? 'index';
        $actionParams = array_filter($controllerData, fn($key) => $key[0] !== '_', ARRAY_FILTER_USE_KEY);
        $actionParams = array_map(fn($param) => is_numeric($param) ? ($param == (int)$param ? (int)$param : (float)$param) : $param, $actionParams);
        
        // Create controller instance
        /** @var Controller $controller */
        $controller = $container->createInstance($controllerData['_controller']);
        $reflection = new \ReflectionClass($controller);
        
        $this->invokeResourceGuards($reflection, $container, $actionName);
        $controller->startup();
        $this->invokeActions($reflection, $container, $controller, $actionName, $actionParams);
        
        // Send response if still not sent
        $controller->getResponse()->sendRawResponse();
    }
    
    /**
     * @param \ReflectionClass<Controller> $reflection
     * @param Container $container
     * @param string $actionName
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function invokeResourceGuards(\ReflectionClass $reflection, Container $container, string $actionName): void
    {
        $grc = array_map(fn($attribute) => $attribute->newInstance(), $reflection->getAttributes(ResourceGuard::class));
        $grm = array_map(fn($attribute) => $attribute->newInstance(), $reflection->getMethod($actionName)->getAttributes(ResourceGuard::class));
        
        /** @var ResourceGuard[] $guards */
        $guards = array_merge($grc, $grm);
        
        foreach ($guards as $guard) {
            /** @var ResourceResolver $instance */
            $instance = $container->getByType(ResourceResolver::class, false) ?: $container->createInstance(ResourceResolver::class);
            $instance->beforeAction($guard->resources);
        }
    }
    
    /**
     * @param \ReflectionClass<Controller> $reflection
     * @param Container $container
     * @param Controller $controller
     * @param string $actionName
     * @param array<string, mixed> $actionParams
     * @return void
     * @throws \ReflectionException
     */
    private function invokeActions(\ReflectionClass $reflection, Container $container, Controller $controller, string $actionName, array $actionParams): void
    {
        $actionModels = $actionRequests = [];
        
        foreach ($reflection->getMethod($actionName)->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof \ReflectionNamedType && class_exists($type->getName())) {
                $instance = $container->getByType($type->getName(), false) ?: $container->createInstance($type->getName());
                if ($instance instanceof IRequest) {
                    $actionRequests[$parameter->getName()] = $instance;
                } else {
                    $actionModels[$parameter->getName()] = $instance;
                }
            }
        }
        
        // Execute action
        if (count($actionRequests) === 1) {
            $key = array_key_first($actionRequests);
            $request = $actionRequests[$key];
            
            $actionModels[$key] = $request;
            $controller->$actionName(...$actionModels);
            
            $data = $controller->getRequest()->getRequestData();
            $data = array_merge($actionParams, $data);
            
            $schema = $request->schema();
            if (count($schema) !== 0) {
                try {
                    $schemaData = $controller->getRequest()->validate($data, $schema);
                    $data = array_merge($data, $schemaData ?: []);
                } catch (ValidationException $exception) {
                    $controller->getResponse()->sendError($exception->getMessages());
                }
            }
            $request->process($data);
        } else {
            $params = array_merge($actionParams, $actionModels);
            $controller->$actionName(...$params);
        }
    }
}