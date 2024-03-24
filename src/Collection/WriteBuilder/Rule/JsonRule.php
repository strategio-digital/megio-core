<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class JsonRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid JSON";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
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