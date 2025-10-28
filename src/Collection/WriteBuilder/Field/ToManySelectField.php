<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\Helper\JoinableLabel;
use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Rule\Base\IRule;
use Megio\Collection\WriteBuilder\Serializer\ToManySerializer;
use Megio\Collection\WriteBuilder\WriteBuilder;

class ToManySelectField extends BaseField
{
    /** @var array<int, SelectField\Item> */
    protected array $items = [];

    /**
     * @param class-string $reverseEntity
     * @param IRule[] $rules
     * @param array<string, bool|float|int|string|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected string $reverseEntity,
        protected string $primaryKey = 'id',
        protected array $rules = [],
        protected array $attrs = [],
        protected bool $disabled = false,
        protected bool $mapToEntity = true,
        protected mixed $value = new UndefinedValue(),
        protected mixed $defaultValue = new UndefinedValue(),
    ) {
        parent::__construct(
            $this->name,
            $this->label,
            $this->rules,
            [],
            [],
            $this->attrs,
            $this->disabled,
            $this->mapToEntity,
            $this->value,
            $this->defaultValue,
        );
    }

    public function renderer(): string
    {
        return 'select-field-renderer';
    }

    public function setBuilder(WriteBuilder $builder): void
    {
        parent::setBuilder($builder);

        $em = $builder->getEntityManager();

        /** @var array<int, mixed> $data */
        $data = $em->getRepository($this->reverseEntity)
            ->createQueryBuilder('e')
            ->select('e')
            ->getQuery()
            ->getArrayResult();

        $this->items = array_map(fn(
            $item,
        ) => new SelectField\Item($item['id'], JoinableLabel::fromArray($item, $this->reverseEntity)), $data);
        $this->serializers[] = new ToManySerializer($this->reverseEntity, $this->primaryKey);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['params']['items'] = array_map(fn(
            $item,
        ) => $item->toArray(), $this->items);
        $data['params']['multiple'] = true;
        return $data;
    }
}
