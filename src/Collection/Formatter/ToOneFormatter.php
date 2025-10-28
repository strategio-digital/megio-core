<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\Formatter\Base\BaseFormatter;
use Megio\Collection\Helper\JoinableLabel;
use ReflectionException;

class ToOneFormatter extends BaseFormatter
{
    /**
     * @throws ReflectionException
     * @throws CollectionException
     *
     * @return array{value: string, label: string}|null
     */
    public function format(
        mixed $value,
        string $key,
    ): ?array {
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            throw new CollectionException("Value '{$value}' must be array.");
        }

        $schema = $this->builder->getMetadata()->getFullSchemaReflectedByDoctrine();

        $joins = array_merge(
            $schema->getOneToOneColumns(),
            $schema->getOneToManyColumns(),
            $schema->getManyToOneColumns(),
            $schema->getManyToManyColumns(),
        );

        $joinable = array_values(array_filter($joins, fn(
            $item,
        ) => $item['name'] === $key));
        $reverseEntity = $joinable[0]['reverseEntity'] ?? null;

        if ($reverseEntity === null) {
            throw new CollectionException("Reverse entity not found for '{$key}'");
        }

        return [
            'label' => JoinableLabel::fromArray($value, $reverseEntity),
            'value' => $value['id'],
        ];
    }
}
