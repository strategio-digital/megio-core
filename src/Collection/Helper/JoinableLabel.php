<?php
declare(strict_types=1);

namespace Megio\Collection\Helper;

use BackedEnum;
use ReflectionException;
use ReflectionMethod;
use UnitEnum;

class JoinableLabel
{
    /**
     * @param array<string, mixed> $data
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    public static function fromArray(
        array $data,
        string $className,
    ): string {
        $instance = new $className();
        $method = new ReflectionMethod($className, 'getJoinableLabel');

        /** @var array{fields: string[], format: string} $describer */
        $describer = $method->invoke($instance);
        unset($instance);

        $values = [];
        foreach ($describer['fields'] as $field) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                if ($value instanceof BackedEnum) {
                    $value = $value->value;
                } elseif ($value instanceof UnitEnum) {
                    $value = $value->name;
                }
                $values[] = $value;
            }
        }

        return sprintf($describer['format'], ...$values);
    }
}
