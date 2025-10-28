<?php
declare(strict_types=1);

namespace Megio\Collection;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use InvalidArgumentException;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use ReflectionNamedType;
use ReflectionProperty;

/**
 *  Doctrine mapping:
 *  https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/basic-mapping.html#doctrine-mapping-types
 *
 * @phpstan-type UnionColumn array{
 *     name: string,
 *     type: string,
 *     unique: bool,
 *     nullable: bool,
 *     maxLength: int|null,
 *     defaultValue: mixed
 * }
 * @phpstan-type JoinableColumn array{
 *     name: string,
 *     type: string,
 *     unique: bool,
 *     nullable: bool,
 *     maxLength: int|null,
 *     defaultValue: mixed,
 *     reverseEntity: class-string,
 *     reverseField: string|null,
 * }
 */
class RecipeDbSchema
{
    /** @var UnionColumn[] */
    private array $unionColumns = [];

    /** @var JoinableColumn[] */
    private array $oneToOneColumns = [];

    /** @var JoinableColumn[] */
    private array $oneToManyColumns = [];

    /** @var JoinableColumn[] */
    private array $manyToOneColumns = [];

    /** @var JoinableColumn[] */
    private array $manyToManyColumns = [];

    public function addUnionColumn(
        Column $attr,
        ReflectionProperty $prop,
    ): void {
        $propType = $prop->getType();
        $nullable = $attr->nullable;
        $type = $attr->type;

        // Fallback to union types
        if ($type === null) {
            $type = ($propType instanceof ReflectionNamedType
                ? $propType->getName()
                : $propType)
                ?? '@unknown';
        }

        // Ensure $type is string for mb_strtolower
        $typeString = is_string($type) ? $type : (string)$type;

        $maxLength = $attr->length;
        if ($maxLength === null && $typeString === 'string') {
            $maxLength = 255;
        }

        $default = new UndefinedValue();
        if (array_key_exists('default', $attr->options)) {
            $default = $attr->options['default'];
        }

        $this->unionColumns[] = [
            'name' => $prop->getName(),
            'type' => mb_strtolower($typeString),
            'unique' => $attr->unique,
            'nullable' => $nullable,
            'maxLength' => $maxLength,
            'defaultValue' => $default,
        ];
    }

    public function addOneToOneColumn(
        OneToOne $attr,
        ReflectionProperty $prop,
    ): void {
        if ($attr->targetEntity === null) {
            throw new InvalidArgumentException('Attribute targetEntity is required');
        }

        $reverseField = $attr->mappedBy;
        if ($reverseField === null) {
            $reverseField = $attr->inversedBy;
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

    public function addOneToManyColumn(
        OneToMany $attr,
        ReflectionProperty $prop,
    ): void {
        if ($attr->targetEntity === null) {
            throw new InvalidArgumentException('Attribute targetEntity is required');
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

    public function addManyToOneColumn(
        ManyToOne $attr,
        ReflectionProperty $prop,
    ): void {
        if ($attr->targetEntity === null) {
            throw new InvalidArgumentException('Attribute targetEntity is required');
        }

        $this->manyToOneColumns[] = [
            'name' => $prop->getName(),
            'type' => 'many_to_one',
            'unique' => false,
            'nullable' => true,
            'maxLength' => null,
            'defaultValue' => null,
            'reverseEntity' => $attr->targetEntity,
            'reverseField' => $attr->inversedBy,
        ];
    }

    public function addManyToManyColumn(
        ManyToMany $attr,
        ReflectionProperty $prop,
    ): void {
        $reverseField = $attr->mappedBy;
        if ($reverseField === null) {
            $reverseField = $attr->inversedBy;
        }

        $this->manyToManyColumns[] = [
            'name' => $prop->getName(),
            'type' => 'many_to_many',
            'unique' => false,
            'nullable' => true,
            'maxLength' => null,
            'defaultValue' => null,
            'reverseEntity' => $attr->targetEntity,
            'reverseField' => $reverseField,
        ];
    }

    /** @return UnionColumn[] */
    public function getUnionColumns(): array
    {
        return $this->unionColumns;
    }

    /** @return JoinableColumn[] */
    public function getOneToOneColumns(): array
    {
        return $this->oneToOneColumns;
    }

    /** @return JoinableColumn[] */
    public function getOneToManyColumns(): array
    {
        return $this->oneToManyColumns;
    }

    /** @return JoinableColumn[] */
    public function getManyToOneColumns(): array
    {
        return $this->manyToOneColumns;
    }

    /** @return JoinableColumn[] */
    public function getManyToManyColumns(): array
    {
        return $this->manyToManyColumns;
    }
}
