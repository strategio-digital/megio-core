<?php
declare(strict_types=1);

namespace Megio\Http\Serializer;

use Exception;
use Megio\Http\Serializer\Validator\ValidatorInterface;

use function json_encode;

/**
 * @phpstan-import-type ValidationErrors from ValidatorInterface
 */
class RequestSerializerException extends Exception
{
    /**
     * @param ValidationErrors $errors
     */
    public function __construct(
        private readonly array $errors = [],
        string $message = "Request deserialization failed",
    ) {
        $this->message = $message . ': ' . json_encode($errors);
        parent::__construct($this->message);
    }

    /**
     * @return ValidationErrors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
