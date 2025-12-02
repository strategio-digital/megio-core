<?php
declare(strict_types=1);

namespace Megio\Translation\Exception;

interface TranslatableExceptionInterface
{
    /**
     * Returns translation key (e.g., 'user.error.not_found')
     */
    public function getTranslationKey(): string;

    /**
     * Returns parameters for ICU MessageFormat (e.g., ['name' => 'John'])
     *
     * @return array<string, mixed>
     */
    public function getTranslationParams(): array;
}
