<?php
declare(strict_types=1);

namespace Megio\Collection;

use Doctrine\ORM\Mapping\Column;
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
 * @phpstan-type OneToOneArray array{
 *     name: string,
 *     type: string,
 *     reverseEntity: class-string,
 *     reverseField: string,
 *     unique: bool,
 *     nullable: bool,
 *     maxLength: int|null,
 *     defaultValue: mixed
 * }
 */
class RecipeDbSchema
{
    /** @var UnionColumnArray[] */
    private array $unionColumns = [];
    
    /** @var OneToOneArray[] */
    private array $oneToOneColumns = [];
    
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
            'reverseEntity' => $attr->targetEntity,
            'reverseField' => $reverseField,
            'unique' => false,
            'nullable' => false,
            'maxLength' => null,
            'defaultValue' => null,
        ];
    }
    
    /** @return UnionColumnArray[] */
    public function getUnionColumns(): array
    {
        return $this->unionColumns;
    }
    
    /** @return OneToOneArray[] */
    public function getOneToOneColumns(): array
    {
        return $this->oneToOneColumns;
    }
}