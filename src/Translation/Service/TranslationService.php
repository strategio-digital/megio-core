<?php

declare(strict_types=1);

namespace Megio\Translation\Service;

use Megio\Helper\EnvConvertor;
use Megio\Translation\Facade\TranslationLoaderFacade;
use Megio\Translation\Formatter\IcuMessageFormatter;
use Nette\Neon\Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Translator as SymfonyTranslator;

use function assert;
use function count;
use function explode;

class TranslationService
{
    private SymfonyTranslator $symfonyTranslator;

    public function __construct(
        private readonly TranslationLoaderFacade $loaderFacade,
    ) {
        // Use custom IcuMessageFormatter for ICU MessageFormat support (plurals, select, etc.)
        $this->symfonyTranslator = new SymfonyTranslator($this->getDefaultLocale(), new IcuMessageFormatter());
        $this->symfonyTranslator->setFallbackLocales($this->getFallbackLocales());
        $this->symfonyTranslator->addLoader('array', new ArrayLoader());
    }

    public function getDefaultLocale(): string
    {
        return EnvConvertor::toString($_ENV['TRANSLATIONS_DEFAULT_LOCALE']);
    }

    /**
     * @return array<string>
     */
    public function getFallbackLocales(): array
    {
        return explode(
            separator: ',',
            string: EnvConvertor::toString($_ENV['TRANSLATIONS_FALLBACK_LOCALES']),
        );
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
        $locale = $locale ?? $this->getDefaultLocale();
        $this->loadMessages($locale);

        return $this->symfonyTranslator->trans($key, $params, null, $locale);
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
        $catalogue = $this->symfonyTranslator->getCatalogue($locale);
        assert($catalogue instanceof MessageCatalogue === true);

        return $catalogue->all();
    }

    public function invalidateCache(?string $locale = null): void
    {
        $this->loaderFacade->invalidateCache($locale);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function loadMessages(string $locale): void
    {
        // Get all locales to load: requested + fallbacks
        $localesToLoad = array_unique(array_merge([$locale], $this->getFallbackLocales()));

        foreach ($localesToLoad as $localeToLoad) {
            // Check if already loaded
            $catalogue = $this->symfonyTranslator->getCatalogue($localeToLoad);
            assert($catalogue instanceof MessageCatalogue === true);

            if (count($catalogue->all()) > 0) {
                continue;
            }

            // Load from facade (handles cache, .neon, and database)
            $messages = $this->loaderFacade->loadMessages($localeToLoad);

            $this->symfonyTranslator->addResource('array', $messages, $localeToLoad);
        }
    }
}
