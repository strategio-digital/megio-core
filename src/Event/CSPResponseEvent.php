<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CSPResponseEvent implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onResponse']
        ];
    }
    
    public function onResponse(ResponseEvent $event): void
    {
        $headers = [
            //'Content-Security-Policy' => "default-src 'nonce-'",
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=()',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Xss-Protection' => '1; mode=block',
            'X-Content-Type-Options' => 'nosniff',
            'X-Powered-By' => 'Strategio SaaS'
        ];
        
        foreach ($headers as $key => $value) {
            $event->getResponse()->headers->set($key, $value);
        }
    }
}