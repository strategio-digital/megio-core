<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class AnyOfRule extends BaseRule
{
    /**
     * @param array<int, bool|float|int|string|null> $keys
     */
    public function __construct(
        protected array   $keys,
        protected ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function message(): string
    {
        $keys = implode(', ', $this->keys);
        return $this->message ?: "Field must be any of '{$keys}'";
    }

    /**
     * Return true if validation is passed
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();

        if (in_array($value, $this->keys, true)) {
            return true;
        }

        return false;
    }
}
