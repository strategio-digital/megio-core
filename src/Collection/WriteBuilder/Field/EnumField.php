<?php declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Field\SelectField\Item;
use Megio\Collection\WriteBuilder\Serializer\CallableSerializer;
use ReflectionClass;

class EnumField extends SelectField
{
    /**
     * @param class-string $enumClassName
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected string $enumClassName,
        protected array $rules = [],
        protected array $serializers = [],
        protected array $formatters = [],
        protected array $attrs = [],
        protected bool $disabled = false,
        protected bool $mapToEntity = true,
        protected mixed $value = new UndefinedValue(),
        protected mixed $defaultValue = new UndefinedValue(),
    ) {
        $isEnum = (new ReflectionClass($this->enumClassName))->isEnum();

        if (!$isEnum) {
            throw new CollectionException('Parameter $enumClassName must be an enum class name.');
        }

        if (count($this->serializers) === 0) {
            $this->serializers = [
                new CallableSerializer(fn(
                    $value,
                ) => $value ? $this->enumClassName::from($value) : null),
            ];
        }

        $this->items = array_map(fn(
            $item,
        ) => new Item($item->value, $item->value), $this->enumClassName::cases());

        parent::__construct(
            $this->name,
            $this->label,
            $this->items,
            $this->rules,
            $this->serializers,
            $this->formatters,
            $this->attrs,
            $this->disabled,
            $this->mapToEntity,
            $this->value,
            $this->defaultValue,
        );
    }
}
