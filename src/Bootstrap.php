<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework;

use Framework\Debugger\Logger;
use Framework\Helper\Path;
use Nette\Bridges\DITracy\ContainerPanel;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Response;
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
        
        // Register DI panel
        Debugger::getBar()->addPanel(new ContainerPanel($container));
        
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
        
        return $container;
    }
}