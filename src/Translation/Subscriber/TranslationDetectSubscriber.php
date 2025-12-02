<?php

declare(strict_types=1);

namespace Megio\Translation\Subscriber;

use Megio\Translation\Facade\LanguageFacade;
use Megio\Translation\Translator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use function is_string;

final readonly class TranslationDetectSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Translator $translator,
        private LanguageFacade $languageFacade,
    ) {}

    /**
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // Priority must be lower than RouterListener (32) to have route attributes available
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale = $request->attributes->get('locale');

        if (is_string($locale) === false) {
            return;
        }

        $browserHeader = $request->headers->get('Accept-Language');
        $posixLocale = $this->languageFacade->recognizeLanguagePosix($locale, $browserHeader);

        if ($posixLocale === null) {
            $response = new JsonResponse(
                data: [
                    'success' => false,
                    'message' => "Language '{$locale}' is not supported.",
                ],
                status: Response::HTTP_NOT_FOUND,
            );

            $event->setResponse($response);
            $event->stopPropagation();
            return;
        }

        $this->translator->setPosix($posixLocale);
    }
}
