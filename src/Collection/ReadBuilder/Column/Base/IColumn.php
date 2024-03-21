<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column\Base;

interface IColumn
{
    public function renderer(): string;
    public function getKey(): string;
    public function getName(): string;
    public function isSortable() : bool;
    public function isFilterable() : bool;
    public function isSearchable() : bool;
    public function isVisible() : bool;
    
    /** @return array{
     *     renderer: string,
     *     key: string,
     *     name: string,
     *     sortable: bool,
     *     filterable: bool,
     *     searchable: bool,
     *     visible: bool
     * }
     */
    public function toArray(): array;
}