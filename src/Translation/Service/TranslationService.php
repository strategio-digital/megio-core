<?php

declare(strict_types=1);

namespace Megio\Translation\Service;

use Megio\Database\EntityManager;
use Megio\Helper\EnvConvertor;
use Megio\Helper\Path;
use Megio\Translation\Cache\TranslationCache;
use Megio\Translation\Formatter\IcuMessageFormatter;
use Megio\Translation\Parser\NeonParser;
use Nette\Neon\Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;

use function array_merge;
use function count;
use function explode;
use function is_dir;

class TranslationService
{
    private SymfonyTranslator $translator;

    private string $defaultLocale;

    public function __construct(
        private readonly EntityManager $em,
        private readonly NeonParser $neonParser,
        private readonly TranslationCache $translationCache,
    ) {
        $this->defaultLocale = EnvConvertor::toString($_ENV['TRANSLATIONS_DEFAULT_LOCALE']);
        $fallbackLocalesString = EnvConvertor::toString($_ENV['TRANSLATIONS_FALLBACK_LOCALES']);
        $fallbackLocales = explode(',', $fallbackLocalesString);

        // Use custom IcuMessageFormatter for ICU MessageFormat support (plurals, select, etc.)
        $this->translator = new SymfonyTranslator($this->defaultLocale, new IcuMessageFormatter());
        $this->translator->setFallbackLocales($fallbackLocales);
        $this->translator->addLoader('array', new ArrayLoader());
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * @param array<string, mixed> $params
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function trans(
        string $key,
        array $params = [],
        ?string $locale = null,
    ): string {
        $locale = $locale ?? $this->defaultLocale;
        $this->loadMessages($locale);

        return $this->translator->trans($key, $params, null, $locale);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     *
     * @return array<string, string>
     */
    public function getAllMessages(string $locale): array
    {
        $this->loadMessages($locale);
        return $this->translator->getCatalogue($locale)->all();
    }

    public function invalidateCache(?string $locale = null): void
    {
        if ($locale !== null) {
            $this->translationCache->delete("translations.{$locale}");
        } else {
            $this->translationCache->clear();
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function loadMessages(string $locale): void
    {
        // Check if already loaded
        if (count($this->translator->getCatalogue($locale)->all()) > 0) {
            return;
        }

        // Try cache
        if ($this->translationCache->has("translations.{$locale}") === true) {
            $messages = $this->translationCache->get("translations.{$locale}");
            if ($messages !== null) {
                $this->translator->addResource('array', $messages, $locale);
                return;
            }
        }

        // Load from .neon + DB
        $messages = array_merge(
            $this->loadFromNeon($locale),
            $this->loadFromDatabase($locale),
        );

        $this->translator->addResource('array', $messages, $locale);
        $this->translationCache->set("translations.{$locale}", $messages, 3600);
    }

    /**
     * @throws Exception
     *
     * @return array<string, string>
     */
    private function loadFromNeon(string $locale): array
    {
        $messages = [];
        $appDir = Path::appDir();

        if (is_dir($appDir) === false) {
            return $messages;
        }

        // Find all .locale.{locale}.neon files
        // Example: app/User/user.locale.cs_CZ.neon
        $finder = new Finder();
        $finder->files()
            ->in($appDir)
            ->name("*.locale.{$locale}.neon");

        foreach ($finder as $file) {
            $parsed = $this->neonParser->parseFileToFlatten($file->getRealPath());
            $messages = array_merge($messages, $parsed);
        }

        return $messages;
    }

    /**
     * @return array<string, string>
     */
    private function loadFromDatabase(string $locale): array
    {
        $messages = [];

        $translations = $this->em->getTranslationRepo()->findByLanguageCode($locale, false);

        foreach ($translations as $translation) {
            $fullKey = $translation->getDomain() . '.' . $translation->getKey();
            $messages[$fullKey] = $translation->getValue();
        }

        return $messages;
    }
}
