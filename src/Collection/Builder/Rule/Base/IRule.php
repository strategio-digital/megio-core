<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Rule\Base;

use Megio\Collection\Builder\Field\Base\IField;

interface IRule
{
    public function name(): string;
    
    public function message(): string;
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool;
    
    /**
     * Structured description for usage in front-end form
     * @return array{name: string, message: string, params: array<string,mixed>}
     */
    public function toArray(): array;
    
    
    public function setField(IField $field): void;
    
    /** @param IField[] $fields */
    public function setRelatedFields(array $fields): void;
    
    /** @param IRule[] $rules */
    public function setRelatedRules(array $rules): void;
}