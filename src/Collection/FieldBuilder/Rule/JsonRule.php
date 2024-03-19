<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Rule;

use Megio\Collection\FieldBuilder\Rule\Base\BaseRule;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class JsonRule extends BaseRule
{
    public function name(): string
    {
        return 'json';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' must be a valid JSON";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        $nullable = array_filter($this->relatedRules, fn($rule) => $rule->name() === 'nullable');
        
        if (count($nullable) !== 0 && $value === null) {
            return true;
        }
        
        if (is_array($value)) {
            try {
                Json::encode($value);
                return true;
            } catch (JsonException) {
            }
        }
        
        return false;
    }
}