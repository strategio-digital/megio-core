<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column\Base;

abstract class BaseColumn implements IColumn
{
    public function __construct(
        protected string $key,
        protected string $name,
        protected bool   $sortable = false,
        protected bool   $visible = true,
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