<?php

declare(strict_types=1);

namespace Megio\Translation\Http\Request;

use Megio\Helper\EnvConvertor;
use Megio\Http\Request\AbstractRequest;
use Megio\Translation\Resolver\PosixResolver;
use Megio\Translation\TranslationManager;
use Nette\Neon\Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslationsFetchRequest extends AbstractRequest
{
    private const int CACHE_TTL_IN_SECONDS = 7200;

    public function __construct(
        private readonly TranslationManager $translationManager,
        private readonly PosixResolver $posixResolver,
    ) {}

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function process(Request $request): Response
    {
        $locale = $request->attributes->getString('locale');

        if ($this->posixResolver->isPosixFormat($locale) === false) {
            return new JsonResponse(
                data: [
                    'success' => false,
                    'message' => "Invalid locale format '{$locale}'. Expected POSIX format (e.g., cs_CZ).",
                ],
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        $messages = $this->translationManager->getAllMessages($locale);
        $response = $this->json($messages);

        if (EnvConvertor::toBool($_ENV['TRANSLATIONS_ENABLE_CACHE']) === true) {
            $response->setMaxAge(self::CACHE_TTL_IN_SECONDS);
        }

        $response->setPublic();

        return $response;
    }
}
