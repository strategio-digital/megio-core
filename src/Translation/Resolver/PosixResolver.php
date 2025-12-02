<?php

declare(strict_types=1);

namespace Megio\Translation\Resolver;

use Megio\Database\Entity\Translation\Language;
use Megio\Database\EntityManager;

use function explode;
use function preg_match;
use function str_replace;
use function strpos;
use function substr;
use function trim;

final readonly class PosixResolver
{
    // Regex patterns for locale validation
    public const string LOCALE_SHORT_PATTERN = '[a-z]{2}';

    public const string LOCALE_POSIX_PATTERN = '[a-z]{2}_[A-Z]{2}';

    public function __construct(
        private EntityManager $em,
    ) {}

    /**
     * Resolves POSIX locale from input (can be shortCode or posix)
     *
     * @param string $locale Input locale (e.g., "cs" or "cs_CZ")
     * @param string|null $browserHeader Accept-Language header from browser
     *
     * @return string|null POSIX locale or null if language does not exist
     */
    public function resolve(
        string $locale,
        ?string $browserHeader = null,
    ): ?string {
        // Try direct match on POSIX (e.g., cs_CZ)
        if ($this->isPosixFormat($locale) === true) {
            return $this->resolveFromPosix($locale);
        }

        // Try shortCode (e.g., cs)
        if ($this->isShortFormat($locale) === true) {
            return $this->resolveFromShortCode($locale, $browserHeader);
        }

        // Invalid locale format, fallback to default
        return $this->getDefaultPosix();
    }

    /**
     * Checks if locale is in POSIX format
     */
    public function isPosixFormat(string $locale): bool
    {
        return preg_match('/^' . self::LOCALE_POSIX_PATTERN . '$/', $locale) === 1;
    }

    /**
     * Checks if locale is in short format
     */
    public function isShortFormat(string $locale): bool
    {
        return preg_match('/^' . self::LOCALE_SHORT_PATTERN . '$/', $locale) === 1;
    }

    private function resolveFromPosix(string $posix): ?string
    {
        $language = $this->em->getLanguageRepo()->findOneByPosix($posix);

        if ($language !== null && $language->isEnabled() === true) {
            return $language->getPosix();
        }

        return $this->getDefaultPosix();
    }

    private function resolveFromShortCode(
        string $shortCode,
        ?string $browserHeader,
    ): ?string {
        $languages = $this->em->getLanguageRepo()->findByShortCode($shortCode);

        // Filter only enabled languages
        $enabled = $languages->filter(
            static fn(Language $lang) => $lang->isEnabled() === true,
        );

        if ($enabled->isEmpty() === true) {
            return $this->getDefaultPosix();
        }

        // If we have browserHeader, try to find a match
        if ($browserHeader !== null) {
            $browserLocales = $this->parseBrowserHeader($browserHeader);
            foreach ($browserLocales as $browserLocale) {
                foreach ($enabled as $language) {
                    if ($language->getPosix() === $browserLocale) {
                        return $language->getPosix();
                    }
                }
            }
        }

        // Return first enabled language found
        $first = $enabled->first();

        if ($first === false) {
            return $this->getDefaultPosix();
        }

        return $first->getPosix();
    }

    private function getDefaultPosix(): ?string
    {
        $default = $this->em->getLanguageRepo()->findDefault();
        return $default?->getPosix();
    }

    /**
     * Parses Accept-Language header into array of POSIX locales
     *
     * Input example: "cs-CZ,cs;q=0.9,en-US;q=0.8,en;q=0.7"
     * Output: ["cs_CZ", "cs", "en_US", "en"]
     *
     * - Splits by comma
     * - Removes quality factor (;q=0.9)
     * - Converts dash to underscore (cs-CZ -> cs_CZ)
     * - Returns array ordered by browser priority
     *
     * @return string[]
     */
    private function parseBrowserHeader(string $header): array
    {
        $locales = [];
        $parts = explode(',', $header);

        foreach ($parts as $part) {
            $part = trim($part);

            $semicolon = strpos($part, ';');
            if ($semicolon !== false) {
                $part = substr($part, 0, $semicolon);
            }

            $part = str_replace('-', '_', $part);
            if ($part !== '') {
                $locales[] = $part;
            }
        }

        return $locales;
    }
}
