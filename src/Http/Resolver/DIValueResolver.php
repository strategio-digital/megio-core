<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Resolver;

use Nette\DI\Container;
use Saas\Http\Request\IRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class DIValueResolver implements ValueResolverInterface
{
    public function __construct(protected Container $container)
    {
    }
    
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument
     * @return iterable<int, object>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        
        if ($argumentType && class_exists($argumentType) && is_subclass_of($argumentType, IRequest::class)) {
            $instance = $this->container->createInstance($argumentType);
            $this->container->addService($argumentType, $instance);
            return [$instance];
        }
        
        if ($argumentType && class_exists($argumentType)) {
            /** @var object $instance */
            $instance = $this->container->getByType($argumentType);
            return [$instance];
        }
        
        return [];
    }
}