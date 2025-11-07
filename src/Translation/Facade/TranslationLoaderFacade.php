<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Megio\Translation\Cache\TranslationCache;
use Megio\Translation\Loader\DatabaseTranslationLoader;
use Megio\Translation\Loader\NeonTranslationLoader;
use Nette\Neon\Exception;
use Psr\Cache\InvalidArgumentException;

use function array_merge;

final readonly class TranslationLoaderFacade
{
    public function __construct(
        private NeonTranslationLoader $neonLoader,
        private DatabaseTranslationLoader $databaseLoader,
        private TranslationCache $translationCache,
    ) {}

    /**
     * Load translations for given locale from all sources (cache -> neon -> database)
     *
     * @throws InvalidArgumentException
     * @throws Exception
     *
     * @return array<string, string>
     */
    public function loadMessages(string $locale): array
    {
        // Try cache first
        if ($this->translationCache->has("translations.{$locale}") === true) {
            $messages = $this->translationCache->get("translations.{$locale}");
            if ($messages !== null) {
                return $messages;
            }
        }

        // Load from .neon + database
        $messages = array_merge(
            $this->neonLoader->load($locale),
            $this->databaseLoader->load($locale),
        );

        // Store in cache
        $this->translationCache->set("translations.{$locale}", $messages, 3600);

        return $messages;
    }

    /**
     * Check if messages are cached for given locale
     *
     * @throws InvalidArgumentException
     */
    public function isCached(string $locale): bool
    {
        return $this->translationCache->has("translations.{$locale}");
    }

    /**
     * Invalidate cache for specific locale or all locales
     */
    public function invalidateCache(?string $locale = null): void
    {
        if ($locale !== null) {
            $this->translationCache->delete("translations.{$locale}");
        } else {
            $this->translationCache->clear();
        }
    }
}
