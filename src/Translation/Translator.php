<?php

declare(strict_types=1);

namespace Megio\Translation;

use Megio\Translation\Helper\PosixHelper;
use Nette\Localization\Translator as ITranslator;
use Stringable;

use function count;
use function is_array;

class Translator implements ITranslator
{
    private ?string $posix = null;

    public function __construct(
        private readonly TranslationManager $translationManager,
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

        return $this->translationManager->getDefaultPosixFromEnv();
    }

    /**
     * Returns short code (first two letters) of the locale
     */
    public function getShortCode(): string
    {
        return PosixHelper::extractShortCode($this->getPosix());
    }

    /**
     * Returns locale in BCP 47 format for HTML lang attribute
     */
    public function getBcp47Locale(): string
    {
        return PosixHelper::toBcp47($this->getPosix());
    }

    /**
     * @return array<string>
     */
    public function getPosixFallbacks(): array
    {
        return $this->translationManager->getPosixFallbacks();
    }

    public function getDefaultPosixFromEnv(): string
    {
        return $this->translationManager->getDefaultPosixFromEnv();
    }

    public function getDefaultShortCodeFromEnv(): string
    {
        return PosixHelper::extractShortCode($this->getDefaultPosixFromEnv());
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

        return $this->translationManager->trans(
            key: $key,
            params: $params,
            posix: $this->getPosix(),
        );
    }
}
