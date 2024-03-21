<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column\Base;

interface IColumn
{
    public function renderer(): string;
    public function getKey(): string;
    public function getName(): string;
    public function isSortable() : bool;
    public function isVisible() : bool;
    
    /** @return array{
     *     renderer: string,
     *     key: string,
     *     name: string,
     *     sortable: bool,
     *     visible: bool
     * }
     */
    public function toArray(): array;
}