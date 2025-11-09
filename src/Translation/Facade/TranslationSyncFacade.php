<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Megio\Database\EntityManager;

final readonly class TranslationSyncFacade
{
    public function __construct(
        private EntityManager $em,
    ) {}

    /**
     * Synchronize is_from_source flag for all translations based on default language
     */
    public function syncFromSource(): void
    {
        $defaultLanguage = $this->em->getLanguageRepo()->findDefault();

        if ($defaultLanguage === null) {
            return;
        }

        $allTranslations = $this->em->getTranslationRepo()->findAll();

        foreach ($allTranslations as $translation) {
            $shouldBeSource = $translation->getLanguage()->getId() === $defaultLanguage->getId();

            if ($translation->isFromSource() !== $shouldBeSource) {
                $translation->setIsFromSource($shouldBeSource);
            }
        }

        $this->em->flush();
    }
}
