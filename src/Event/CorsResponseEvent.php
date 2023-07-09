<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsResponseEvent implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest']
        ];
    }
    
    public function onRequest(RequestEvent $event): void
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