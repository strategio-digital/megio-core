<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Tracy\Debugger;

class CorsRequest implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::FINISH_REQUEST => ['onFinishRequest']
        ];
    }
    
    /*
     * Cannot be written as pure SymfonyResponse - it's important to use header() function.
     * Because of Tracy/Debugger overrides these headers hardcoded.
     */
    public function onFinishRequest(FinishRequestEvent $event): void
    {
        $request = $event->getRequest();
        
        if ($request->headers->has('origin')) {
            header('Access-Control-Allow-Origin: ' . $request->headers->get('Origin'));
            header('Access-Control-Allow-Credentials: true');
        }

        // Allow all HTTP methods
        if ($request->getMethod() === 'OPTIONS') {
            if ($request->headers->has('Access-Control-Request-Method')) {
                header('Access-Control-Allow-Methods: *');
            }

            if ($request->headers->has('Access-Control-Request-Headers')) {
                header('Access-Control-Allow-Headers: ' . $request->headers->get('Access-Control-Request-Headers'));
            }
            exit;
        }
    }
}