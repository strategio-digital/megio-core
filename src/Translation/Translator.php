<?php

declare(strict_types=1);

namespace Megio\Translation;

use Megio\Translation\Service\TranslationService;
use Nette\Localization\Translator as ITranslator;
use Stringable;

use function count;
use function is_array;

class Translator implements ITranslator
{
    private ?string $locale = null;

    public function __construct(
        private readonly TranslationService $translationService,
    ) {}

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        if ($this->locale !== null) {
            return $this->locale;
        }

        return $this->translationService->getDefaultLocale();
    }

    public function translate(
        string|Stringable $message,
        mixed ...$parameters,
    ): string|Stringable {
        $key = (string)$message;

        // Latte passes parameters as single array argument
        // So ...$parameters becomes [0 => ['name' => 'Jan', ...]]
        // We need to extract the actual parameters from the first element
        $params = [];
        if (count($parameters) === 1 && is_array($parameters[0])) {
            $params = $parameters[0];
        } elseif (count($parameters) > 0) {
            // Fallback for direct parameter passing
            $params = $parameters;
        }

        return $this->translationService->trans(
            key: $key,
            params: $params,
            locale: $this->getLocale(),
        );
    }
}
