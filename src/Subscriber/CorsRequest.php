<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsRequest implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999],
        ];
    }
    
    public function onKernelException(ExceptionEvent $event): void
    {
        $response = $event->getResponse();
        $headers = $event->getRequest()->headers->get('Access-Control-Request-Headers');
        
        if ($response) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', '*');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            
            if ($headers) {
                $response->headers->set('Access-Control-Allow-Headers', $headers);
            }
            
            $this->forceHeadersOnlyInDevelopmentModeBecauseOfTracy($event->getRequest());
        }
    }
    
    public function onKernelRequest(RequestEvent $event): void
    {
        // Don't do anything if it's not the master request.
        if (!$event->isMainRequest()) {
            return;
        }
        
        $request = $event->getRequest();
        $method = $request->getRealMethod();
        
        if (Request::METHOD_OPTIONS === $method) {
            $response = new Response();
            $event->setResponse($response);
            $this->forceHeadersOnlyInDevelopmentModeBecauseOfTracy($event->getRequest());
        }
    }
    
    public function onKernelResponse(ResponseEvent $event): void
    {
        // Don't do anything if it's not the master request.
        if (!$event->isMainRequest()) {
            return;
        }
        
        $response = $event->getResponse();
        $headers = $event->getRequest()->headers->get('Access-Control-Request-Headers');
        
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', '*');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        
        if ($headers) {
            $response->headers->set('Access-Control-Allow-Headers', $headers);
        }
        
        $this->forceHeadersOnlyInDevelopmentModeBecauseOfTracy($event->getRequest());
    }
    
    
    /*
     * Cannot be written as pure SymfonyResponse - it's important to use header() function.
     * Because of Tracy/Debugger overrides these headers hardcoded.
     */
    private function forceHeadersOnlyInDevelopmentModeBecauseOfTracy(Request $request): void
    {
        if ($_ENV['APP_ENV_MODE'] === 'develop') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: *');
            
            $headers = $request->headers->get('Access-Control-Request-Headers');
            
            if ($headers) {
                header('Access-Control-Allow-Headers: ' . $headers);
            }
        }
    }
}