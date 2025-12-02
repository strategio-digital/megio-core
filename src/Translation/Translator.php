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
    private ?string $posix = null;

    public function __construct(
        private readonly TranslationService $translationService,
    ) {}

    public function setPosix(string $posix): void
    {
        $this->posix = $posix;
    }

    public function getPosix(): string
    {
        if ($this->posix !== null) {
            return $this->posix;
        }

        return $this->translationService->getDefaultPosixFromEnv();
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
