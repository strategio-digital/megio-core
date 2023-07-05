<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas;

use Latte\Bridges\Tracy\LattePanel;
use Latte\Engine;
use Saas\Debugger\Logger;
use Saas\Extension\Vite\Vite;
use Saas\Helper\Path;
use Saas\Helper\Thumbnail;
use Saas\Http\Resolver\LinkResolver;
use Saas\Security\Response\Cors;
use Nette\Bridges\DITracy\ContainerPanel;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tracy\Debugger;

class Bootstrap
{
    public function projectRootPath(string $rootPath): Bootstrap
    {
        /** @var string $realPath */
        $realPath = realpath($rootPath);
        Path::setProjectPath($realPath);
        return $this;
    }
    
    /**
     * @param array<string> $configPaths
     * @param float $startedAt
     * @return Container
     */
    public function configure(array $configPaths, float $startedAt): Container
    {
        // Load .env
        $_ENV = array_merge(getenv(), $_ENV);
        $envPath = Path::wwwDir() . '/../.env';
        
        date_default_timezone_set($_ENV['TZ']);
        
        if (file_exists($envPath)) {
            $dotenv = new Dotenv();
            $dotenv->loadEnv($envPath);
        }
        
        // Setup debugger
        Debugger::setLogger(new Logger(Path::logDir()));
        Debugger::enable($_ENV['APP_ENV_MODE'] === 'develop' ? Debugger::DEVELOPMENT : Debugger::PRODUCTION, Path::logDir());
        Debugger::$strictMode = E_ALL;
        
        // Setup DI container
        $loader = new ContainerLoader(Path::tempDir() . '/di', $_ENV['APP_ENV_MODE'] === 'develop');
        $class = $loader->load(function ($compiler) use ($configPaths) {
            foreach ($configPaths as $configPath) {
                $compiler->loadConfig(realpath($configPath));
            }
        });
        
        /** @var Container $container */
        $container = new $class;
        $container->parameters['startedAt'] = $startedAt;
        
        /** @var Engine $latte */
        $latte = $container->getByType(Engine::class);
        $latte->setAutoRefresh($_ENV['APP_ENV_MODE'] === 'develop');
        $latte->setTempDirectory(Path::tempDir() . '/latte');
        
        /** @var Vite $vite */
        $vite = $container->getByType(Vite::class);
        $latte->addFunction('vite', fn(string $source, bool $isEntryPoint = false) => $isEntryPoint ? $vite->resolveEntrypoint($source) : $vite->resolveSource($source));
        
        /** @var LinkResolver $linkResolver */
        $linkResolver = $container->getByType(LinkResolver::class);
        $latte->addFunction('route', fn(string $name, array $params = [], int $path = UrlGeneratorInterface::ABSOLUTE_PATH) => $linkResolver->link($name, $params, $path));
        $latte->addFunction('thumbnail', fn(string $path, ?int $width, ?int $height, string $method = 'EXACT', int $quality = 80) => new Thumbnail($path, $width, $height, $method, $quality));
        
        // Register DI panels
        Debugger::getBar()->addPanel(new ContainerPanel($container));
        Debugger::getBar()->addPanel(new LattePanel($latte));
        
        /** @var Response $response */
        $response = $container->getByType(Response::class);
        $response->headers->add([
            //'Content-Security-Policy' => "default-src 'nonce-'",
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=()',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Xss-Protection' => '1; mode=block',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        
        /** @var Cors $cors */
        $cors = $container->getByType(Cors::class);
        $cors->allow();
        
        return $container;
    }
}