<?php

declare(strict_types=1);

namespace Megio\Translation;

use Megio\Translation\Service\TranslationService;
use Nette\Localization\Translator as ITranslator;
use Stringable;

use function count;
use function is_array;
use function str_replace;
use function substr;

class Translator implements ITranslator
{
    private ?string $posix = null;

    public function __construct(
        private readonly TranslationService $translationService,
    ) {}

    /**
     * Sets locale in POSIX format (e.g., en_US)
     */
    public function setPosix(string $posix): void
    {
        $this->posix = $posix;
    }

    /**
     * Returns locale in POSIX format (e.g., en_US)
     */
    public function getPosix(): string
    {
        if ($this->posix !== null) {
            return $this->posix;
        }

        return $this->translationService->getDefaultPosixFromEnv();
    }

    /**
     * Returns short code (first two letters) of the locale
     */
    public function getShortCode(): string
    {
        return substr($this->getPosix(), 0, 2);
    }

    /**
     * Returns locale in BCP 47 format for HTML lang attribute
     */
    public function getBcp47Locale(): string
    {
        return str_replace('_', '-', $this->getPosix());
    }

    /**
     * @return array<string>
     */
    public function getPosixFallbacks(): array
    {
        return $this->translationService->getPosixFallbacks();
    }

    public function getDefaultPosixFromEnv(): string
    {
        return $this->translationService->getDefaultPosixFromEnv();
    }

    public function getDefaultShortCodeFromEnv(): string
    {
        return substr($this->getDefaultPosixFromEnv(), 0, 2);
    }

    public function translate(
        string|Stringable $message,
        mixed ...$parameters,
    ): string {
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
            posix: $this->getPosix(),
        );
    }
}
