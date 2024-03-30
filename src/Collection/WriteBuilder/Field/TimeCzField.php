<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\Formatter\TimeCzFormatter;
use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Rule\TimeCzRule;
use Megio\Collection\WriteBuilder\Serializer\DateTimeSerializer;

class TimeCzField extends BaseField
{
    public function renderer(): string
    {
        return 'time-cz-field-renderer';
    }
    
    /**
     * @param \Megio\Collection\WriteBuilder\Rule\Base\IRule[] $rules
     * @param \Megio\Collection\Formatter\Base\IFormatter[] $formatters
     * @param array<string, string|int|float|bool|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected array  $rules = [],
        protected array  $serializers = [new DateTimeSerializer()],
        protected array  $formatters = [new TimeCzFormatter()],
        protected array  $attrs = [],
        protected bool   $disabled = false,
        protected bool   $mapToEntity = true,
        protected mixed  $defaultValue = new UndefinedValue()
    )
    {
        $this->rules[] = new TimeCzRule();
        parent::__construct(
            $this->name,
            $this->label,
            $this->rules,
            $this->serializers,
            $this->formatters,
            $this->attrs,
            $this->disabled,
            $this->mapToEntity,
            $this->defaultValue
        );
    }
}