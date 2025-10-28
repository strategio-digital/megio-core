<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\Formatter\Base\IFormatter;
use Megio\Collection\Formatter\DateTimeIntervalFormatter;
use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Rule\Base\IRule;
use Megio\Collection\WriteBuilder\Rule\DateTimeIntervalRule;
use Megio\Collection\WriteBuilder\Serializer\DateTimeIntervalSerializer;

class DateTimeIntervalField extends BaseField
{
    /**
     * @param IRule[] $rules
     * @param IFormatter[] $formatters
     * @param array<string, bool|float|int|string|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected array  $rules = [],
        protected array  $serializers = [new DateTimeIntervalSerializer()],
        protected array  $formatters = [new DateTimeIntervalFormatter()],
        protected array  $attrs = [],
        protected bool   $disabled = false,
        protected bool   $mapToEntity = true,
        protected mixed  $value = new UndefinedValue(),
        protected mixed  $defaultValue = new UndefinedValue(),
    ) {
        $this->rules[] = new DateTimeIntervalRule();
        parent::__construct(
            $this->name,
            $this->label,
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

    public function renderer(): string
    {
        return 'date-time-interval-field-renderer';
    }
}
