<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\CrudHelper;

class EntityMetadata
{
    /**
     * @param class-string $className
     * @param string $tableName
     * @param array<int, string> $visibleFields
     * @param array<int, mixed> $propsSchema
     */
    public function __construct(
        public string $className,
        public string $tableName,
        public array  $visibleFields,
        public array  $propsSchema = []
    )
    {
    }
    
    public function getQuerySelect(string $alias): string
    {
        return implode(', ', array_map(fn($field) => $alias . '.' . $field, $this->visibleFields));
    }
}