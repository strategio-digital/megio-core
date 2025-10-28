<?php
declare(strict_types=1);

namespace Megio\Collection;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\SearchBuilder\SearchBuilder;
use Megio\Collection\WriteBuilder\WriteBuilder;

interface ICollectionRecipe
{
    /** @return class-string */
    public function source(): string;

    public function key(): string;

    public function name(): string;

    /** @return  array<string, 'ASC'|'DESC'> */
    public function sort(): array;

    /** @throws CollectionException */
    public function read(
        ReadBuilder $builder,
        CollectionRequest $request,
    ): ReadBuilder;

    /** @throws CollectionException */
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

    /** @throws CollectionException */
    public function getEntityMetadata(): RecipeEntityMetadata;
}
