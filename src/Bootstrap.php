<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas;

use Latte\Bridges\Tracy\LattePanel;
use Latte\Engine;
use Nette\DI\Compiler;
use Nette\Neon\Neon;
use Saas\Debugger\Logger;
use Saas\Extension\Extension;
use Saas\Extension\Vite\Vite;
use Saas\Helper\Path;
use Saas\Helper\Thumbnail;
use Saas\Http\Resolver\LinkResolver;
use Nette\Bridges\DITracy\ContainerPanel;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Symfony\Component\Dotenv\Dotenv;
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
     * @param string[] $configPaths
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
        
        // Create DI container
        $container = $this->createContainer($configPaths);
        $container->parameters['startedAt'] = $startedAt;
        
        // Initialize extensions
        if (method_exists($container, 'initialize')) {
            $container->initialize();
        }
        
        /** @var Engine $latte */
        $latte = $container->getByType(Engine::class);
        $latte->setAutoRefresh($_ENV['APP_ENV_MODE'] === 'develop');
        $latte->setTempDirectory(Path::tempDir() . '/latte');
        
        /** @var Vite $vite */
        $vite = $container->getByType(Vite::class);
        
        /** @var LinkResolver $linkResolver */
        $linkResolver = $container->getByType(LinkResolver::class);
        
        $latte->addFunction('vite', fn(string $source, bool $isEntryPoint = false) => $isEntryPoint ? $vite->resolveEntrypoint($source) : $vite->resolveSource($source));
        $latte->addFunction('route', fn(string $name, array $params = [], int $path = UrlGeneratorInterface::ABSOLUTE_PATH) => $linkResolver->link($name, $params, $path));
        $latte->addFunction('thumbnail', fn(string $path, ?int $width, ?int $height, string $method = 'EXACT', int $quality = 80) => new Thumbnail($path, $width, $height, $method, $quality));
        
        // Register DI panels
        Debugger::getBar()->addPanel(new ContainerPanel($container));
        Debugger::getBar()->addPanel(new LattePanel($latte));
        
        return $container;
    }
    
    /**
     * @param string[] $configPaths
     * @return \Nette\DI\Container
     */
    protected function createContainer(array $configPaths): Container
    {
        $loader = new ContainerLoader(Path::tempDir() . '/di', $_ENV['APP_ENV_MODE'] === 'develop');
        
        /** @var Container $class */
        $class = $loader->load(function (Compiler $compiler) use ($configPaths) {
            // Load & merge configs
            foreach ($configPaths as $configPath) {
                $compiler->loadConfig($configPath);
            }
            
            // Add "extensions" extension
            $compiler->addExtension('extensions', new Extension());
            
            // Register custom extensions
            if (array_key_exists(0, $configPaths)) {
                $neon = Neon::decodeFile($configPaths[0]);
                if (array_key_exists('extensions', $neon) && $neon['extensions']) {
                    foreach ($neon['extensions'] as $name => $extension) {
                        /** @var \Nette\DI\CompilerExtension $instance */
                        $instance = new $extension();
                        $compiler->addExtension($name, $instance);
                    }
                }
            }
        });
        
        return new $class;
    }
}