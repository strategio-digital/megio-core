<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Field\Base;

use Megio\Collection\Builder\Builder;
use Megio\Collection\Builder\Rule\Base\IRule;

interface IField
{
    public function renderer(): string;
    
    public function getType(): FieldNativeType;
    
    public function mappedToEntity(): bool;
    
    
    public function getName(): string;
    
    public function getLabel(): string;
    
    public function addRule(IRule $rule): void;
    
    /** @return \Megio\Collection\Builder\Rule\Base\IRule[] */
    public function getRules(): array;
    
    /** @return array<string, string|int|float|bool|null> */
    public function getAttrs(): array;
    
    
    public function getValue(): string|int|float|bool|null;
    
    public function setValue(string|int|float|bool|null $value): void;
    
    public function addError(string $message): void;
    
    
    public function setBuilder(Builder $builder): void;
    
    public function getBuilder(): Builder;
    
    /** @return array<string, mixed> */
    public function toArray(): array;
}