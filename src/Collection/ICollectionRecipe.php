<?php
declare(strict_types=1);

namespace Megio\Collection;

use Megio\Collection\SearchBuilder\SearchBuilder;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\ReadBuilder\ReadBuilder;

interface ICollectionRecipe
{
    /** @return class-string */
    public function source(): string;

    public function key(): string;

    public function name(): string;

    /** @return  array<string, 'ASC'|'DESC'> */
    public function sort(): array;

    /** @throws \Megio\Collection\Exception\CollectionException */
    public function read(
        ReadBuilder $builder,
        CollectionRequest $request,
    ): ReadBuilder;

    /** @throws \Megio\Collection\Exception\CollectionException */
    public function readAll(
        ReadBuilder $builder,
        CollectionRequest $request,
    ): ReadBuilder;

    public function create(
        WriteBuilder $builder,
        CollectionRequest $request,
    ): WriteBuilder;

    public function update(
        WriteBuilder $builder,
        CollectionRequest $request,
    ): WriteBuilder;

    public function search(
        SearchBuilder $builder,
        CollectionRequest $request,
    ): SearchBuilder;

    /** @throws \Megio\Collection\Exception\CollectionException */
    public function getEntityMetadata(): RecipeEntityMetadata;
}