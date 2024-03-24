<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class AnyOfRule extends BaseRule
{
    /**
     * AnyOfRule constructor.
     * @param array<int, string|int|float|bool|null> $keys
     * @param string|null $message
     */
    public function __construct(
        protected array       $keys,
        protected string|null $message = null
    )
    {
        parent::__construct(message: $message);
    }
    
    public function message(): string
    {
        $keys = implode(', ', $this->keys);
        return $this->message ?: "Field must be any of '{$keys}'";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (in_array($value, $this->keys)) {
            return true;
        }
        
        return false;
    }
}