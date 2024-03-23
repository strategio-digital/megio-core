<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class JsonStringRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid JSON string";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        $nullable = array_filter($this->relatedRules, fn($rule) => $rule::class === NullableRule::class);
        
        if (count($nullable) !== 0 && $value === null) {
            return true;
        }
        
        if (!is_string($value)) {
            return false;
        }
        
        try {
            Json::decode($value);
            return true;
        } catch (JsonException) {
        }
        
        return false;
    }
}