<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\Formatter\Base\IFormatter;
use Megio\Collection\Formatter\DateCzFormatter;
use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Rule\Base\IRule;
use Megio\Collection\WriteBuilder\Rule\DateCzRule;
use Megio\Collection\WriteBuilder\Serializer\DateTimeSerializer;

class DateCzField extends BaseField
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
        protected array  $serializers = [new DateTimeSerializer()],
        protected array  $formatters = [new DateCzFormatter()],
        protected array  $attrs = [],
        protected bool   $disabled = false,
        protected bool   $mapToEntity = true,
        protected mixed  $value = new UndefinedValue(),
        protected mixed  $defaultValue = new UndefinedValue(),
    ) {
        $this->rules[] = new DateCzRule();
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
        return 'date-cz-field-renderer';
    }
}
