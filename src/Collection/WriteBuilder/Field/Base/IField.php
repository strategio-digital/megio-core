<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field\Base;

use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\Rule\Base\IRule;

interface IField
{
    public function renderer(): string;
    
    public function mappedToEntity(): bool;
    
    public function getName(): string;
    
    public function getLabel(): string;
    
    public function isDisabled(): bool;
    
    public function addRule(IRule $rule): void;
    
    /** @return \Megio\Collection\WriteBuilder\Rule\Base\IRule[] */
    public function getRules(): array;
    
    public function removeRule(IRule $rule): void;
    
    /** @return \Megio\Collection\Formatter\Base\IFormatter[] */
    public function getFormatters(): array;
    
    /** @return \Megio\Collection\WriteBuilder\Serializer\Base\ISerializer[] */
    public function getSerializers(): array;
    
    /** @return array<string, string|int|float|bool|null> */
    public function getAttrs(): array;
    
    /** @return mixed|UndefinedValue */
    public function getValue(): mixed;
    
    /** @return mixed|UndefinedValue */
    public function getDefaultValue(): mixed;
    
    public function setValue(mixed $value): void;
    
    public function addError(string $message): void;
    
    public function setBuilder(WriteBuilder $builder): void;
    
    public function getBuilder(): WriteBuilder;
    
    /** @return array<string, mixed> */
    public function toArray(): array;
}