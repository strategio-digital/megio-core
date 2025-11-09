<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Doctrine\ORM\Exception\ORMException;
use Megio\Database\Entity\Translation\Language;
use Megio\Database\EntityManager;
use Megio\Helper\EnvConvertor;
use Megio\Translation\Facade\Dto\LanguageStatisticsDto;
use Megio\Translation\Facade\Exception\LanguageFacadeException;
use Megio\Translation\Http\Request\Dto\LanguageCreateDto;
use Megio\Translation\Http\Request\Dto\LanguageUpdateDto;

final readonly class LanguageFacade
{
    public function __construct(
        private EntityManager $em,
    ) {}

    /**
     * @throws LanguageFacadeException
     * @throws ORMException
     */
    public function createLanguage(LanguageCreateDto $dto): Language
    {
        $exists = $this->em->getLanguageRepo()->findByCode($dto->code);

        if ($exists !== null) {
            throw new LanguageFacadeException('Language with this code already exists');
        }

        // If setting as default, unset other defaults
        if ($dto->isDefault === true) {
            $this->em->getLanguageRepo()->unsetAllDefaults();
        }

        $language = new Language();
        $language->setCode($dto->code);
        $language->setName($dto->name);
        $language->setIsDefault($dto->isDefault);
        $language->setIsEnabled($dto->isEnabled);

        $this->em->persist($language);
        $this->em->flush();

        return $language;
    }

    /**
     * @throws LanguageFacadeException
     * @throws ORMException
     */
    public function updateLanguage(LanguageUpdateDto $dto): Language
    {
        $language = $this->em->getLanguageRepo()->find($dto->id);

        if ($language === null) {
            throw new LanguageFacadeException('Language not found');
        }

        if ($dto->isDefault === true) {
            $this->em->getLanguageRepo()->unsetAllDefaults();
        }

        $language->setName($dto->name);
        $language->setIsDefault($dto->isDefault);
        $language->setIsEnabled($dto->isEnabled);

        $this->em->flush();

        return $language;
    }

    /**
     * Synchronize default language from ENV with database
     *
     * @throws ORMException
     */
    public function syncDefaultLanguage(): void
    {
        $defaultLocale = EnvConvertor::toString($_ENV['TRANSLATIONS_DEFAULT_LOCALE']);
        $allLanguages = $this->em->getLanguageRepo()->findAll();

        // Find default language from ENV and unset all defaults
        $defaultLanguage = null;
        foreach ($allLanguages as $language) {
            if ($language->getCode() === $defaultLocale) {
                $defaultLanguage = $language;
            }

            // Unset default for all languages
            $language->setIsDefault(false);
        }

        // Create if doesn't exist
        if ($defaultLanguage === null) {
            $defaultLanguage = new Language();
            $defaultLanguage->setCode($defaultLocale);
            $defaultLanguage->setName($defaultLocale);
            $this->em->persist($defaultLanguage);
        }

        // Set default flag for selected language
        $defaultLanguage->setIsDefault(true);
        $defaultLanguage->setIsEnabled(true);

        $this->em->flush();
    }

    /**
     * @return LanguageStatisticsDto[]
     */
    public function getLanguageStatistics(): array
    {
        $languages = $this->em->getLanguageRepo()->findAll();
        $statistics = [];

        foreach ($languages as $language) {
            $total = $this->em->getTranslationRepo()->countByLanguage($language);
            $fromSource = $this->em->getTranslationRepo()->countFromSourceByLanguage($language);
            $deleted = $this->em->getTranslationRepo()->countDeletedByLanguage($language);

            $statistics[] = new LanguageStatisticsDto(
                code: $language->getCode(),
                name: $language->getName(),
                isDefault: $language->isDefault(),
                isEnabled: $language->isEnabled(),
                total: $total,
                fromSource: $fromSource,
                fromDb: $total - $fromSource,
                deleted: $deleted,
            );
        }

        return $statistics;
    }
}
