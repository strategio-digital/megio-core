<?php
declare(strict_types=1);

namespace Megio\Http\Resolver;

use Nette\DI\Container;
use Megio\Http\Controller\Base\IController;

class ControllerResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver
{
    public function __construct(protected Container $container)
    {
        parent::__construct();
    }
    
    public function instantiateController(string $class): object
    {
        /** @var \Megio\Http\Controller\Base\IController $instance */
        $instance = $this->container->createInstance($class);
        
        if (is_subclass_of($instance::class, IController::class)) {
            $instance->__inject($this->container);
        }
        
        return $instance;
    }
}