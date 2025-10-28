<?php
declare(strict_types=1);

namespace Megio\Subscriber;

use GuzzleHttp\Psr7\Uri;
use Nette\Utils\Strings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectToWww implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest'],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $envUri = new Uri($_ENV['APP_URL']);
        $envHost = $envUri->getHost();

        $isProductionUrl = $envHost !== 'localhost';
        $isNonWww = !Strings::contains($request->getHost(), 'www.');
        $isTargetUrl = str_replace('www.', '', $envHost) === $request->getHost();

        if ($isProductionUrl && $isNonWww && $isTargetUrl) {
            $url = $request->getScheme() . '://www.' . $request->getHttpHost() . $request->getRequestUri();
            $event->setResponse(new RedirectResponse($url, 301));
        }
    }
}
