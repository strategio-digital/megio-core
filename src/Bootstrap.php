<?php
declare(strict_types=1);

namespace Megio;

use Exception;
use Megio\Debugger\JsonLogstashLogger;
use Megio\Extension\Extension;
use Megio\Helper\Path;
use Nette\Bridges\DITracy\ContainerPanel;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Neon\Neon;
use Symfony\Component\Dotenv\Dotenv;
use Tracy\Debugger;
use Tracy\ILogger;

use const E_ALL;

class Bootstrap
{
    protected bool $invokedLogger = false;

    /**
     * @throws Exception
     */
    public function projectRootPath(string $rootPath): Bootstrap
    {
        $realPath = realpath($rootPath);

        if ($realPath === false) {
            throw new Exception("Invalid project root path-");
        }

        Path::setProjectPath($realPath);

        // Load environment variables
        $_ENV = array_merge(getenv(), $_ENV);
        $envPath = Path::wwwDir() . '/../.env';

        if (file_exists($envPath)) {
            $dotenv = new Dotenv();
            $dotenv->loadEnv($envPath);
        }

        return $this;
    }

    public function logger(ILogger $logger): Bootstrap
    {
        // Setup debugger
        Debugger::enable(
            $_ENV['APP_ENVIRONMENT'] === 'develop' ? Debugger::Development : Debugger::Production,
            Path::logDir(),
        );
        Debugger::$strictMode = E_ALL;
        Debugger::setLogger($logger);

        if (array_key_exists('TRACY_EDITOR', $_ENV) && array_key_exists('TRACY_EDITOR_MAPPING', $_ENV)) {
            Debugger::$editor = $_ENV['TRACY_EDITOR'];
            Debugger::$editorMapping = ['/var/www/html' => $_ENV['TRACY_EDITOR_MAPPING']];
        }

        $this->invokedLogger = true;

        return $this;
    }

    /**
     */
    public function configure(
        string $configPath,
        float $startedAt,
    ): Container {
        if ($this->invokedLogger === false) {
            $this->logger(new JsonLogstashLogger());
        }

        date_default_timezone_set($_ENV['APP_TIME_ZONE']);

        // Create DI container
        $container = $this->createContainer($configPath);
        $container->parameters['startedAt'] = $startedAt;

        // Register Tracy DI panel
        $container->addService('tracy.bar', Debugger::getBar());
        Debugger::getBar()->addPanel(new ContainerPanel($container));

        // Initialize extensions
        $container->initialize();

        return $container;
    }

    /**
     */
    protected function createContainer(string $configPath): Container
    {
        $loader = new ContainerLoader(Path::tempDir() . '/di', $_ENV['APP_ENVIRONMENT'] === 'develop');

        $class = $loader->load(function (
            Compiler $compiler,
        ) use (
            $configPath,
        ): void {
            // Load entry-point config
            $compiler->loadConfig($configPath);

            // Add "extensions" extension
            $compiler->addExtension('extensions', new Extension());

            // Register custom extensions
            $neon = Neon::decodeFile($configPath);
            if (array_key_exists('extensions', $neon) === true && is_array($neon['extensions']) === true) {
                foreach ($neon['extensions'] as $name => $extension) {
                    /** @var CompilerExtension $instance */
                    $instance = new $extension();
                    $compiler->addExtension($name, $instance);
                }
            }
        });

        $instance = new $class();
        assert($instance instanceof Container);
        return $instance;
    }
}
