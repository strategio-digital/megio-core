<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\Formatter\Base\BaseFormatter;
use Megio\Database\Interface\IJoinable;

class OneToOneEntityFormatter extends BaseFormatter
{
    public function format(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        
        if ($value instanceof IJoinable === false) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            throw new CollectionException("Value '{$value}' must be class that implements IJoinable interface.");
        }
        
        return $value->getJoinableLabel();
    }
}