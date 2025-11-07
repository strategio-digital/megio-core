<?php

declare(strict_types=1);

namespace Megio\Translation\Http\Request;

use Megio\Http\Request\AbstractRequest;
use Megio\Translation\Service\TranslationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslationsFetchRequest extends AbstractRequest
{
    public function __construct(
        private readonly TranslationService $translationManager,
    ) {}

    public function process(Request $request): Response
    {
        $code = $request->attributes->get('code', 'en_US');
        $messages = $this->translationManager->getAllMessages($code);

        $response = $this->json($messages);
        $response->setMaxAge(3600);
        $response->setPublic();

        return $response;
    }
}
