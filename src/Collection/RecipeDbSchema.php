<?php
declare(strict_types=1);

namespace Megio\Collection;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;

/**
 *  Doctrine mapping:
 *  https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/basic-mapping.html#doctrine-mapping-types
 *
 * @phpstan-type UnionColumnArray array{
 *     name: string,
 *     type: string,
 *     unique: bool,
 *     nullable: bool,
 *     maxLength: int|null,
 *     defaultValue: mixed
 * }
 *
 * @phpstan-type OneToXArray array{
 *     name: string,
 *     type: string,
 *     unique: bool,
 *     nullable: bool,
 *     maxLength: int|null,
 *     defaultValue: mixed,
 *     reverseEntity: class-string,
 *     reverseField: string,
 * }
 */
class RecipeDbSchema
{
    /** @var UnionColumnArray[] */
    private array $unionColumns = [];
    
    /** @var OneToXArray[] */
    private array $oneToOneColumns = [];
    
    /** @var OneToXArray[] */
    private array $oneToManyColumns = [];
    
    public function addUnionColumn(Column $attr, \ReflectionProperty $prop): void
    {
        $propType = $prop->getType();
        $nullable = $attr->nullable;
        $type = $attr->type;
        
        // Fallback to union types
        if ($type === null) {
            $type = $propType instanceof \ReflectionNamedType ? $propType->getName() : $propType ?? '@unknown';
        }
        
        $maxLength = $attr->length;
        if ($maxLength === null && $type === 'string') {
            $maxLength = 255;
        }
        
        $default = new UndefinedValue();
        if (array_key_exists('default', $attr->options)) {
            $default = $attr->options['default'];
        }
        
        $this->unionColumns[] = [
            'name' => $prop->getName(),
            'type' => mb_strtolower($type),
            'unique' => $attr->unique,
            'nullable' => $nullable,
            'maxLength' => $maxLength,
            'defaultValue' => $default,
        ];
    }
    
    public function addOneToOneColumn(OneToOne $attr, \ReflectionProperty $prop): void
    {
        if ($attr->targetEntity === null) {
            throw new \InvalidArgumentException('Attribute targetEntity is required');
        }
        
        $reverseField = $attr->mappedBy;
        if ($reverseField === null) {
            $reverseField = $attr->inversedBy;
        }
        
        if ($reverseField === null) {
            throw new \InvalidArgumentException('Attribute mappedBy or inversedBy is required');
        }
        
        $this->oneToOneColumns[] = [
            'name' => $prop->getName(),
            'type' => 'one_to_one',
            'unique' => false,
            'nullable' => $prop->getType()?->allowsNull() ?? false,
            'maxLength' => null,
            'defaultValue' => null,
            'reverseEntity' => $attr->targetEntity,
            'reverseField' => $reverseField,
        ];
    }
    
    public function addOneToManyColumn(OneToMany $attr, \ReflectionProperty $prop): void
    {
        if ($attr->targetEntity === null) {
            throw new \InvalidArgumentException('Attribute targetEntity is required');
        }
        
        if ($attr->mappedBy === null) {
            throw new \InvalidArgumentException('Attribute mappedBy is required');
        }
        
        $this->oneToManyColumns[] = [
            'name' => $prop->getName(),
            'type' => 'one_to_many',
            'unique' => false,
            'nullable' => false,
            'maxLength' => null,
            'defaultValue' => null,
            'reverseEntity' => $attr->targetEntity,
            'reverseField' => $attr->mappedBy,
        ];
    }
    
    /** @return UnionColumnArray[] */
    public function getUnionColumns(): array
    {
        return $this->unionColumns;
    }
    
    /** @return OneToXArray[] */
    public function getOneToOneColumns(): array
    {
        return $this->oneToOneColumns;
    }
    
    /** @return OneToXArray[] */
    public function getOneToManyColumns(): array
    {
        return $this->oneToManyColumns;
    }
}