<?php

declare(strict_types=1);

namespace Megio\Translation\Http\Request;

use Megio\Http\Request\AbstractRequest;
use Megio\Http\Serializer\RequestSerializerException;
use Megio\Translation\Facade\Exception\TranslationFacadeException;
use Megio\Translation\Facade\TranslationFacade;
use Megio\Translation\Http\Request\Dto\TranslationUpdateDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslationUpdateRequest extends AbstractRequest
{
    public function __construct(
        private readonly TranslationFacade $translationFacade,
    ) {}

    /**
     * @throws RequestSerializerException
     */
    public function process(Request $request): Response
    {
        $dto = $this->requestToDto(TranslationUpdateDto::class);

        try {
            $translation = $this->translationFacade->updateTranslation($dto);
        } catch (TranslationFacadeException $e) {
            return $this->error(['general' => $e->getMessage()]);
        }

        return $this->json([
            'id' => $translation->getId(),
            'key' => $translation->getKey(),
            'domain' => $translation->getDomain(),
            'value' => $translation->getValue(),
        ]);
    }
}
