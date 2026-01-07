<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Doctrine\ORM\Exception\ORMException;
use Megio\Database\Entity\Translation\Language;
use Megio\Database\EntityManager;
use Megio\Helper\EnvConvertor;
use Megio\Translation\Helper\PosixHelper;

final readonly class SyncDefaultLanguageFacade
{
    public function __construct(
        private EntityManager $em,
    ) {}

    /**
     * Synchronize default language from ENV with database
     *
     * @throws ORMException
     */
    public function execute(): void
    {
        $defaultPosix = EnvConvertor::toString($_ENV['TRANSLATIONS_DEFAULT_LOCALE']);
        $allLanguages = $this->em->getLanguageRepo()->findAll();

        // Find default language from ENV and unset all defaults
        $defaultLanguage = null;
        foreach ($allLanguages as $language) {
            if ($language->getPosix() === $defaultPosix) {
                $defaultLanguage = $language;
            }

            // Unset default for all languages
            $language->setIsDefault(false);
        }

        // Create if doesn't exist
        if ($defaultLanguage === null) {
            $defaultLanguage = new Language();
            $defaultLanguage->setPosix($defaultPosix);
            $defaultLanguage->setShortCode(PosixHelper::extractShortCode($defaultPosix));
            $defaultLanguage->setName($defaultPosix);
            $this->em->persist($defaultLanguage);
        }

        // Set default flag for selected language
        $defaultLanguage->setIsDefault(true);
        $defaultLanguage->setIsEnabled(true);

        $this->em->flush();
    }
}
