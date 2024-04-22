<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class EqualRule extends BaseRule
{
    public function __construct(
        protected string  $targetField,
        protected ?string $message = null
    )
    {
        parent::__construct($message);
    }
    
    public function message(): string
    {
        return $this->message ?: "Field must be equal to '{$this->targetField}' field";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        /** @var \Megio\Collection\WriteBuilder\Field\Base\IField|false $targetField */
        $targetField = current(array_filter($this->relatedFields, fn($field) => $field->getName() === $this->targetField));
        
        if (!$targetField) {
            $this->message = "Field '{$this->targetField}' not found in related fields";
            return false;
        }
        
        return $value === $targetField->getValue();
    }
    
    /**
     * Structured description for usage in front-end form
     * @return array{name: string, message: string, params: array<string,mixed>}
     */
    public function toArray(): array
    {
        $validations = parent::toArray();
        $validations['params']['target'] = $this->targetField;
        return $validations;
    }
}