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
        
        // Allow all origins & set cookies or sessions for them
        if ($request->headers->has('Origin')) {
            $response = new Response();
            $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $event->setResponse($response);
            $event->getResponse()?->sendHeaders();
        }
        
        // Allow all HTTP methods
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response();
            if ($request->headers->has('Access-Control-Request-Method')) {
                $response->headers->set('Access-Control-Allow-Methods', '*');
            }
            
            if ($request->headers->has('Access-Control-Request-Headers')) {
                $response->headers->set('Access-Control-Allow-Headers', $request->headers->get('Access-Control-Request-Headers'));
            }
            
            $event->setResponse($response);
            $event->getResponse()?->send();
        }
    }
}