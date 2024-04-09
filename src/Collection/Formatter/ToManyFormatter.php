<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\Formatter\Base\BaseFormatter;
use Megio\Collection\Helper\JoinableLabel;

class ToManyFormatter extends BaseFormatter
{
    /**
     * @param mixed $value
     * @param string $key
     * @return array{label: string, value: string}[]|null
     * @throws \Megio\Collection\Exception\CollectionException|\ReflectionException
     */
    public function format(mixed $value, string $key): ?array
    {
        if ($value === null) {
            return null;
        }
        
        if (!is_array($value)) {
            throw new CollectionException("Value '{$value}' must be array with keys 'value' and 'label'.");
        }
        
        $schema = $this->builder->getMetadata()->getFullSchemaReflectedByDoctrine();
        
        $joins = array_merge(
            $schema->getOneToOneColumns(),
            $schema->getOneToManyColumns(),
            $schema->getManyToOneColumns()
        );
        
        $joinable = array_values(array_filter($joins, fn($item) => $item['name'] === $key));
        $reverseEntity = $joinable[0]['reverseEntity'] ?? null;
        
        if ($reverseEntity === null) {
            throw new CollectionException("Reverse entity not found for '{$key}'");
        }
        
        $result = [];
        foreach ($value as $item) {
            $result[] = [
                'label' => JoinableLabel::fromArray($item, $reverseEntity),
                'value' => $item['id'],
            ];
        }
        
        return $result;
    }
}