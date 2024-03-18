<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Rule;

use Megio\Collection\CollectionException;
use Megio\Collection\Builder\Rule\Base\BaseRule;

class EqualRule extends BaseRule
{
    public function __construct(
        protected string      $target,
        protected string|null $message = null
    )
    {
        parent::__construct($message);
    }
    
    public function name(): string
    {
        return 'equal';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' must be equal to '{$this->target}'";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        /** @var \Megio\Collection\Builder\Field\Base\IField|false $targetField */
        $targetField = current(array_filter($this->relatedFields, fn($field) => $field->getName() === $this->target));
        
        if (!$targetField) {
            $this->message = "Field '{$this->target}' not found in related fields";
            return false;
        }
        
        if ($value !== $targetField->getValue()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Structured description for usage in front-end form
     * @return array{name: string, message: string, params: array<string,mixed>}
     */
    public function toArray(): array
    {
        $validations = parent::toArray();
        $validations['params']['target'] = $this->target;
        return $validations;
    }
}