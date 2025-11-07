<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Megio\Database\Entity\Translation\Translation;
use Megio\Database\EntityManager;
use Megio\Translation\Cache\TranslationCache;
use Megio\Translation\Facade\Exception\TranslationFacadeException;
use Megio\Translation\Http\Request\Dto\TranslationUpdateDto;

final readonly class TranslationFacade
{
    public function __construct(
        private EntityManager $em,
        private TranslationCache $translationCache,
    ) {}

    /**
     * @throws TranslationFacadeException
     */
    public function updateTranslation(TranslationUpdateDto $dto): Translation
    {
        $translation = $this->em->getTranslationRepo()->find($dto->id);

        if ($translation === null) {
            throw new TranslationFacadeException('Translation not found');
        }

        $translation->setValue($dto->value);
        $this->em->flush();

        // Invalidate cache for this language
        $languageCode = $translation->getLanguage()->getCode();
        $this->translationCache->delete("translations.{$languageCode}");

        return $translation;
    }
}
