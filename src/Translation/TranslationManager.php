<?php

declare(strict_types=1);

namespace Megio\Translation;

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

class TranslationManager
{
    private SymfonyTranslator $symfonyTranslator;

    public function __construct(
        private readonly TranslationLoaderFacade $loaderFacade,
    ) {
        // Use custom IcuMessageFormatter for ICU MessageFormat support (plurals, select, etc.)
        $this->symfonyTranslator = new SymfonyTranslator($this->getDefaultPosixFromEnv(), new IcuMessageFormatter());
        $this->symfonyTranslator->setFallbackLocales($this->getPosixFallbacks());
        $this->symfonyTranslator->addLoader('array', new ArrayLoader());
    }

    public function getDefaultPosixFromEnv(): string
    {
        return EnvConvertor::toString($_ENV['TRANSLATIONS_DEFAULT_LOCALE']);
    }

    /**
     * @return array<string>
     */
    public function getPosixFallbacks(): array
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
        ?string $posix = null,
    ): string {
        $posix = $posix ?? $this->getDefaultPosixFromEnv();
        $this->loadMessages($posix);

        return $this->symfonyTranslator->trans($key, $params, null, $posix);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     *
     * @return array<string, string>
     */
    public function getAllMessages(string $posix): array
    {
        $this->loadMessages($posix);
        $catalogue = $this->symfonyTranslator->getCatalogue($posix);
        assert($catalogue instanceof MessageCatalogue === true);

        return $catalogue->all();
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function loadMessages(string $posix): void
    {
        // Get all locales to load: requested + fallbacks
        $posixToLoads = array_unique(array_merge([$posix], $this->getPosixFallbacks()));

        foreach ($posixToLoads as $posixToLoad) {
            // Check if already loaded
            $catalogue = $this->symfonyTranslator->getCatalogue($posixToLoad);
            assert($catalogue instanceof MessageCatalogue === true);

            if (count($catalogue->all()) > 0) {
                continue;
            }

            // Load from facade (handles cache, .neon, and database)
            $messages = $this->loaderFacade->loadMessages($posixToLoad);

            $this->symfonyTranslator->addResource('array', $messages, $posixToLoad);
        }
    }
}
