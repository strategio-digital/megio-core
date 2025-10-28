<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field\Base;

use Megio\Collection\Formatter\Base\IFormatter;
use Megio\Collection\WriteBuilder\Rule\Base\IRule;
use Megio\Collection\WriteBuilder\Serializer\Base\ISerializer;
use Megio\Collection\WriteBuilder\WriteBuilder;

abstract class BaseField implements IField
{
    protected WriteBuilder $builder;

    /** @var string[] */
    protected array $errors = [];

    /**
     * @param IRule[] $rules
     * @param ISerializer[] $serializers
     * @param IFormatter[] $formatters
     * @param array<string, bool|float|int|string|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected array $rules = [],
        protected array $serializers = [],
        protected array $formatters = [],
        protected array $attrs = [],
        protected bool $disabled = false,
        protected bool $mapToEntity = true,
        protected mixed $value = new UndefinedValue(),
        protected mixed $defaultValue = new UndefinedValue(),
    ) {}

    public function addRule(IRule $rule): void
    {
        $this->rules[] = $rule;
    }

    public function mappedToEntity(): bool
    {
        return $this->mapToEntity;
    }

    /**
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function getBuilder(): WriteBuilder
    {
        return $this->builder;
    }

    public function setBuilder(WriteBuilder $builder): void
    {
        $this->builder = $builder;
    }

    public function removeRule(IRule $rule): void
    {
        $this->rules = array_filter($this->rules, fn(
            $r,
        ) => $r::class !== $rule::class);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $rules = array_map(fn(
            $rule,
        ) => $rule->toArray(), $this->getRules());
        $serializers = array_map(fn(
            $serializer,
        ) => $serializer::class, $this->getSerializers());
        $formatters = array_map(fn(
            $formatter,
        ) => $formatter::class, $this->getFormatters());

        return [
            'renderer' => $this->renderer(),
            'disabled' => $this->isDisabled(),
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'rules' => array_values($rules),
            'serializers' => array_values($serializers),
            'formatters' => array_values($formatters),
            'attrs' => $this->getAttrs(),
            'value' => $this->getValue(),
            'default_value' => $this->getDefaultValue(),
            'errors' => $this->getErrors(),
        ];
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    /** @return ISerializer[] */
    public function getSerializers(): array
    {
        return $this->serializers;
    }

    /** @return IFormatter[] */
    public function getFormatters(): array
    {
        return $this->formatters;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /** @return array<string, bool|float|int|string|null> */
    public function getAttrs(): array
    {
        return $this->attrs;
    }

    /**
     * @return mixed|UndefinedValue
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /** @return mixed|UndefinedValue */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
