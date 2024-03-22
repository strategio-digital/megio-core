<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column\Base;

use Megio\Collection\ReadBuilder\Transformer\Base\ITransformer;

abstract class BaseColumn implements IColumn
{
    /**
     * @param ITransformer[] $transformers
     */
    public function __construct(
        protected string $key,
        protected string $name,
        protected bool   $sortable = false,
        protected bool   $visible = true,
        protected array  $transformers = []
    )
    {
    }
    
    public function getKey(): string
    {
        return $this->key;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function isSortable(): bool
    {
        return $this->sortable;
    }
    
    public function isVisible(): bool
    {
        return $this->visible;
    }
    
    /** @return ITransformer[] */
    public function getTransformers(): array
    {
        return $this->transformers;
    }
    
    /** @return array{
     *     renderer: string,
     *     key: string,
     *     name: string,
     *     sortable: bool,
     *     visible: bool
     * }
     */
    public function toArray(): array
    {
        return [
            'renderer' => $this->renderer(),
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'sortable' => $this->isSortable(),
            'visible' => $this->isVisible(),
        ];
    }
}