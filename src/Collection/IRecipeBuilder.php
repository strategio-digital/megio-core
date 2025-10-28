<?php
declare(strict_types=1);

namespace Megio\Collection;

interface IRecipeBuilder
{
    /**
     * @return array<int, mixed>
     */
    public function toArray(): array;
}
