<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Rule;

use Megio\Collection\FieldBuilder\Rule\Base\BaseRule;

class CzPhoneRule extends BaseRule
{
    public function __construct(
        protected string|null $message = null,
        protected bool        $normalize = true
    )
    {
        parent::__construct($message);
    }
    
    public function name(): string
    {
        return 'czPhone';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' must be a valid phone number. Example: +420 000 000 000";
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
        
        if (!is_string($value)) {
            return false;
        }
        
        /** @var string $value */
        $value = preg_replace('/\s+/', '', $value);
        
        // Pokud číslo začíná pouze na 420, je potřeba tuto předponu převést na +420
        if (preg_match('/^420/', $value)) {
            /** @var string $value */
            $value = preg_replace('/^420/', '+420', $value);
        }
        
        // Pokud začáná na 00420, je potřeba tuto předponu převést na +420
        if (preg_match('/^00420/', $value)) {
            /** @var string $value */
            $value = preg_replace('/^00420/', '+420', $value);
        }
        
        // Pokud nezačáná na +420, je potřeba tuto předponu přidat
        if (!preg_match('/^\+420/', $value)) {
            $value = '+420' . $value;
        }
        
        // Validace celého čísla a normalizace
        if (preg_match('/^\+420\d{9}$/', $value)) {
            if ($this->normalize) {
                $this->field->setValue($value);
            }
            return true;
        }
        
        return false;
    }
}