<?php
declare(strict_types=1);

namespace Megio\Translation\Exception;

use Exception;
use Throwable;

class TranslatableException extends Exception implements TranslatableExceptionInterface
{
    /**
     * @param array<string, mixed> $translationParams
     */
    public function __construct(
        private readonly string $translationKey,
        private readonly array $translationParams = [],
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($translationKey, $code, $previous);
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTranslationParams(): array
    {
        return $this->translationParams;
    }
}
