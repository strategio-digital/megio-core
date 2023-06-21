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
     * @param array<int, string> $props
     * @param array<int, mixed> $propsSchema
     * @param array<int, string> $invisibleFields
     */
    public function __construct(
        public string   $className,
        public string   $tableName,
        protected array $props,
        protected array $propsSchema = [],
        protected array $invisibleFields = []
    )
    {
    }
    
    public function getQuerySelect(string $alias): string
    {
        return implode(', ', array_map(fn($prop) => $alias . '.' . $prop, $this->props));
    }
    
    /**
     * @return array<string, mixed>
     */
    public function getSchema(): array
    {
        return [
            'meta' => [
                'table' => $this->tableName,
                'invisible' => $this->invisibleFields
            ],
            'props' => $this->propsSchema
        ];
    }
}