<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Kernel;

use Saas\Extension\Doctrine\Doctrine;
use Saas\Extension\Doctrine\PostgresDefaultSchemaSubscriber;
use Saas\Helper\Path;
use Saas\Http\Resolver\ControllerResolver;
use Saas\Http\Resolver\DIValueResolver;
use Nette\DI\Container;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Tracy\Debugger;

class App
{
    public function __construct(
        protected RequestContext     $context,
        protected ControllerResolver $controllerResolver,
        protected EventDispatcher    $dispatcher,
        protected RouteCollection    $routes,
        protected UrlMatcher         $router,
        protected Request            $request
    )
    {
    }
    
    /**
     * @param \Nette\DI\Container $container
     * @return HttpKernel
     * @throws \Exception
     */
    public function createKernel(Container $container): HttpKernel
    {
        // Controllers & argument resolvers
        $custom = [new DIValueResolver($container)];
        $valueResolvers = array_merge(ArgumentResolver::getDefaultArgumentValueResolvers(), $custom); // @phpstan-ignore-line
        $argumentResolver = new ArgumentResolver(null, $valueResolvers);
        $requestStack = new RequestStack();
        
        // Register subscribers
        $this->dispatcher->addSubscriber(new RouterListener($this->router, $requestStack));
        
        // Routing configurator
        $loader = new PhpFileLoader(new FileLocator(Path::routerDir()));
        $routing = new RoutingConfigurator($this->routes, $loader, Path::routerDir(), '/app.php');
        $routing->import(Path::routerDir() . '/app.php')->stateless();
        
        // HttpKernel
        return new HttpKernel($this->dispatcher, $this->controllerResolver, $requestStack, $argumentResolver);
    }
    
    /**
     * @param \Nette\DI\Container $container
     * @return void
     * @throws \Exception
     */
    public function run(Container $container): void
    {
        $kernel = self::createKernel($container);
        
        try {
            $response = $kernel->handle($this->request);
        } catch (NotFoundHttpException $e) {
            $response = new JsonResponse(['message' => $e->getMessage()], $e->getStatusCode());
        }
        
        // Remember: Tracy overrides Response headers!
        if ($_ENV['APP_ENV_MODE'] === 'develop' && !$response instanceof JsonResponse) {
            Debugger::renderLoader();
        }
        
        $response->send();
        $kernel->terminate($this->request, $response);
    }
    
    /**
     * @param \Nette\DI\Container $container
     * @return void
     * @throws \Exception
     */
    public function cmd(Container $container): void
    {
        /** @var Doctrine $doctrine */
        $doctrine = $container->getByType(Doctrine::class);
        $evm = $doctrine->getEntityManager()->getEventManager();
        $evm->addEventSubscriber(new PostgresDefaultSchemaSubscriber());
        
        $app = new \Symfony\Component\Console\Application();
        $services = $container->findByType(Command::class);
        
        foreach ($services as $name) {
            /** @var Command $command */
            $command = $container->getByName($name);
            $app->add($command);
        }
        
        $app->setDispatcher($this->dispatcher);
        $app->run();
    }
}