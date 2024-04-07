<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Serializer\OneToManyEntitySerializer;
use Megio\Collection\WriteBuilder\Serializer\OneToOneEntitySerializer;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Database\Interface\IJoinable;

class OneToManySelectField extends BaseField
{
    /** @var array<int, SelectField\Item> */
    protected array $items = [];
    
    public function renderer(): string
    {
        return 'select-field-renderer';
    }
    
    /**
     * @param class-string $reverseEntity
     * @param \Megio\Collection\WriteBuilder\Rule\Base\IRule[] $rules
     * @param array<string, string|int|float|bool|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected string $reverseEntity,
        protected string $primaryKey = 'id',
        protected array  $rules = [],
        protected array  $attrs = [],
        protected bool   $disabled = false,
        protected bool   $mapToEntity = true,
        protected mixed  $value = new UndefinedValue(),
        protected mixed  $defaultValue = new UndefinedValue()
    )
    {
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
            $this->defaultValue
        );
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
            ->getResult();
        
        $this->items = array_map(function ($item) {
            if ($item instanceof IJoinable) {
                return new SelectField\Item($item->getId(), $item->getJoinableLabel());
            } else {
                $class = $item::class;
                throw new CollectionException("Entity '{$class}' has to implement IJoinable interface!");
            }
        }, $data);
        
        $this->serializers[] = new OneToManyEntitySerializer($this->reverseEntity, $this->primaryKey);
    }
    
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['params']['items'] = array_map(fn($item) => $item->toArray(), $this->items);
        $data['params']['multiple'] = true;
        return $data;
    }
}