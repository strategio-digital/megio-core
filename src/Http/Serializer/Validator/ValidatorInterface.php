<?php
declare(strict_types=1);

namespace Megio\Http\Serializer\Validator;

/**
 * @phpstan-type ValidationErrors array<string, mixed>
 */
interface ValidatorInterface
{
    /**
     * @param class-string $dtoClass
     * @param array<string, mixed> $data
     *
     * @return ValidationErrors
     */
    public function validate(
        string $dtoClass,
        array $data,
    ): array;
}
