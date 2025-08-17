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
        $instance = $this->container->createInstance($class);
        
        if ($instance instanceof IController === true) {
            $instance->__inject($this->container);
        }
        
        return $instance;
    }
}