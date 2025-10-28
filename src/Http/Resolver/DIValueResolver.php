<?php
declare(strict_types=1);

namespace Megio\Http\Resolver;

use Megio\Http\Request\IRequest;
use Nette\DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class DIValueResolver implements ValueResolverInterface
{
    public function __construct(protected Container $container) {}

    /**
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
