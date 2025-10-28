<?php
declare(strict_types=1);

namespace Megio\Http\Kernel;

use Exception;
use Megio\Helper\Path;
use Megio\Http\Resolver\ControllerResolver;
use Megio\Http\Resolver\DIValueResolver;
use Nette\DI\Container;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
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
        protected RequestContext $context,
        protected ControllerResolver $controllerResolver,
        protected EventDispatcher $dispatcher,
        protected RouteCollection $routes,
        protected UrlMatcher $router,
        protected Request $request,
        protected Container $container,
    ) {}

    /**
     * @throws Exception
     */
    public function cmd(): void
    {
        self::createKernel();

        $app = new Application();
        $services = $this->container->findByType(Command::class);

        foreach ($services as $name) {
            /** @var Command $command */
            $command = $this->container->getByName($name);
            $app->add($command);
        }

        $app->setDispatcher($this->dispatcher);
        $app->run();
    }

    /**
     * @throws Exception
     */
    public function createKernel(): HttpKernel
    {
        // Controllers & argument resolvers
        $custom = [new DIValueResolver($this->container)];
        $defaultResolvers = iterator_to_array(ArgumentResolver::getDefaultArgumentValueResolvers());
        $valueResolvers = array_merge($defaultResolvers, $custom);
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
     * @throws Exception
     */
    public function run(): void
    {
        $trustedProxies = explode(',', $_ENV['APP_TRUSTED_PROXIES']);
        $trustedHeaderSet = Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PROTO
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PREFIX;

        Request::setTrustedProxies($trustedProxies, $trustedHeaderSet);
        $kernel = self::createKernel();

        try {
            $response = $kernel->handle($this->request);
        } catch (NotFoundHttpException $e) {
            $response = new JsonResponse(['message' => $e->getMessage()], $e->getStatusCode());
        }

        // Remember: Tracy overrides Response headers!
        $isHtml = $response->headers->get('content-type') === 'text/html';
        if ($_ENV['APP_ENVIRONMENT'] === 'develop' && $isHtml && !$this->request->isMethod('OPTIONS')) {
            Debugger::renderLoader();
        }

        $response->send();
        $kernel->terminate($this->request, $response);
    }
}
