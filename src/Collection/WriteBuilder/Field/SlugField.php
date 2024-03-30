<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Rule\SlugRule;

class SlugField extends BaseField
{
    public function renderer(): string
    {
        return 'slug-field-renderer';
    }
    
    /**
     * @param \Megio\Collection\WriteBuilder\Rule\Base\IRule[] $rules
     * @param \Megio\Collection\Formatter\Base\IFormatter[] $formatters
     * @param array<string, string|int|float|bool|null> $attrs
     */
    public function __construct(
        protected string  $name,
        protected string  $label,
        protected ?string $slugFrom = null,
        protected array   $rules = [],
        protected array   $serializers = [],
        protected array   $formatters = [],
        protected array   $attrs = [],
        protected bool    $disabled = false,
        protected bool    $mapToEntity = true,
        protected mixed   $defaultValue = new UndefinedValue()
    )
    {
        $this->rules[] = new SlugRule();
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
    
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['params']['slug_from'] = $this->slugFrom;
        return $data;
    }
}