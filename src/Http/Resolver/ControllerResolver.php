<?php
declare(strict_types=1);

namespace Megio\Http\Resolver;

use Megio\Http\Controller\Base\IController;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;

class ControllerResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver
{
    public function __construct(protected Container $container)
    {
        parent::__construct();
    }

    /**
     * @param class-string<object> $class
     *
     * @throws MissingServiceException
     */
    public function instantiateController(string $class): object
    {
        $instance = $this->container->createInstance($class);

        if ($instance instanceof IController === true) {
            $instance->__inject($this->container);
        }

        return $instance;
    }
}
