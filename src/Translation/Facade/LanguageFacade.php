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

    /**
     * Ensure default language from ENV exists in database
     *
     * @throws ORMException
     * @throws LanguageFacadeException
     */
    public function ensureDefaultLanguageExists(): void
    {
        $defaultLocale = EnvConvertor::toString($_ENV['TRANSLATIONS_DEFAULT_LOCALE']);

        if ($defaultLocale === '') {
            throw new LanguageFacadeException(
                'Default locale is not set in environment variable TRANSLATIONS_DEFAULT_LOCALE',
            );
        }

        // Find or create default language
        $language = $this->em->getLanguageRepo()->findByCode($defaultLocale);

        if ($language === null) {
            $language = new Language();
            $language->setCode($defaultLocale);
            $language->setName($defaultLocale);
            $language->setIsDefault(true);
            $language->setIsEnabled(true);
            $this->em->persist($language);
        }

        // Remove default flag from other languages
        $allLanguages = $this->em->getLanguageRepo()->findAll();

        foreach ($allLanguages as $otherLanguage) {
            if (
                $otherLanguage->getId() !== $language->getId()
                && $otherLanguage->isDefault() === true
            ) {
                $otherLanguage->setIsDefault(false);
            }
        }

        $this->em->flush();
    }
}
