<?php

declare(strict_types=1);

namespace Megio\Http\Serializer;

use Megio\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;
use function lcfirst;
use function str_replace;
use function strtr;
use function trim;
use function ucwords;

/**
 * Adapter that bridges Megio Translator to Symfony Validator's TranslatorInterface.
 * Maps Symfony validation messages to NEON translation keys.
 */
readonly class ValidatorTranslatorAdapter implements TranslatorInterface
{
    public function __construct(
        private Translator $translator,
    ) {}

    /**
     * @param array<string, mixed> $parameters
     */
    public function trans(
        string $id,
        array $parameters = [],
        ?string $domain = null,
        ?string $locale = null,
    ): string {
        // Map Symfony message to NEON key
        $neonKey = $this->resolveTranslationKey($id);

        // Convert Symfony parameters to ICU format
        $icuParams = $this->convertToIcuParams($parameters);

        // Translate using Megio Translator
        $translated = $this->translator->translate($neonKey, $icuParams);

        // If translation not found (key returned as-is), fallback to original with Symfony placeholders
        if ($translated === $neonKey) {
            return strtr($id, $parameters);
        }

        return $translated;
    }

    public function getLocale(): string
    {
        return $this->translator->getPosix();
    }

    /**
     * Maps Symfony validation message to NEON translation key.
     */
    private function resolveTranslationKey(string $message): string
    {
        if (array_key_exists($message, ValidatorMessageMap::MAP) === true) {
            return 'validator.' . ValidatorMessageMap::MAP[$message];
        }

        // Fallback: return original message (will trigger fallback in trans())
        return $message;
    }

    /**
     * Converts Symfony {{ placeholder }} format to ICU {placeholder} format.
     *
     * Symfony uses: ['{{ limit }}' => 5, '%count%' => 5]
     * ICU uses: ['limit' => 5, 'count' => 5]
     *
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    private function convertToIcuParams(array $parameters): array
    {
        $result = [];
        foreach ($parameters as $key => $value) {
            // Remove {{ }}, %, and spaces from key
            $cleanKey = trim(str_replace(['{{', '}}', '%', ' '], '', $key));

            // Convert snake_case to camelCase for ICU compatibility
            $icuKey = $this->snakeToCamel($cleanKey);

            $result[$icuKey] = $value;
        }

        return $result;
    }

    private function snakeToCamel(string $input): string
    {
        $result = str_replace('_', '', ucwords($input, '_'));

        return lcfirst($result);
    }
}
