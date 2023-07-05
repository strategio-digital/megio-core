<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Resolver;

use Nette\DI\Container;
use Saas\Http\Controller\Base\IController;

class ControllerResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver
{
    public function __construct(protected Container $container)
    {
        parent::__construct();
    }
    
    public function instantiateController(string $class): object
    {
        /** @var \Saas\Http\Controller\Base\IController $instance */
        $instance = $this->container->createInstance($class);
        
        if (is_subclass_of($instance::class, IController::class)) {
            $instance->__inject($this->container);
        }
        
        return $instance;
    }
}