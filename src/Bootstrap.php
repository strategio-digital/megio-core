<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio;

use Nette\DI\Compiler;
use Nette\Neon\Neon;
use Megio\Debugger\Logger;
use Megio\Extension\Extension;
use Megio\Helper\Path;
use Nette\Bridges\DITracy\ContainerPanel;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Symfony\Component\Dotenv\Dotenv;
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
     * @param string $configPath
     * @param float $startedAt
     * @return Container
     */
    public function configure(string $configPath, float $startedAt): Container
    {
        // Load .env
        $_ENV = array_merge(getenv(), $_ENV);
        $envPath = Path::wwwDir() . '/../.env';
        
        if (file_exists($envPath)) {
            $dotenv = new Dotenv();
            $dotenv->loadEnv($envPath);
        }
        
        date_default_timezone_set($_ENV['APP_TIME_ZONE']);
        
        // Setup debugger
        Debugger::setLogger(new Logger(Path::logDir()));
        Debugger::enable($_ENV['APP_ENV_MODE'] === 'develop' ? Debugger::Development : Debugger::Production, Path::logDir());
        Debugger::$strictMode = E_ALL;
        
        if (array_key_exists('TRACY_EDITOR', $_ENV) && array_key_exists('TRACY_EDITOR_MAPPING', $_ENV)) {
            Debugger::$editor = $_ENV['TRACY_EDITOR'];
            Debugger::$editorMapping = ['/var/www/html' => $_ENV['TRACY_EDITOR_MAPPING']];
        }
        
        // Create DI container
        $container = $this->createContainer($configPath);
        $container->parameters['startedAt'] = $startedAt;
        
        // Register Tracy DI panel
        $container->addService('tracy.bar', Debugger::getBar());
        Debugger::getBar()->addPanel(new ContainerPanel($container));
        
        // Initialize extensions
        if (method_exists($container, 'initialize')) {
            $container->initialize();
        }
        
        return $container;
    }
    
    /**
     * @param string $configPath
     * @return \Nette\DI\Container
     */
    protected function createContainer(string $configPath): Container
    {
        $loader = new ContainerLoader(Path::tempDir() . '/di', $_ENV['APP_ENV_MODE'] === 'develop');
        
        /** @var Container $class */
        $class = $loader->load(function (Compiler $compiler) use ($configPath) {
            // Load entry-point config
            $compiler->loadConfig($configPath);
            
            // Add "extensions" extension
            $compiler->addExtension('extensions', new Extension());
            
            // Register custom extensions
            $neon = Neon::decodeFile($configPath);
            if (array_key_exists('extensions', $neon) && $neon['extensions']) {
                foreach ($neon['extensions'] as $name => $extension) {
                    /** @var \Nette\DI\CompilerExtension $instance */
                    $instance = new $extension();
                    $compiler->addExtension($name, $instance);
                }
            }
        });
        
        return new $class;
    }
}