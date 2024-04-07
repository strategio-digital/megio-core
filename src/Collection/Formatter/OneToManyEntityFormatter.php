<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Doctrine\Common\Collections\Collection;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\Formatter\Base\BaseFormatter;
use Megio\Database\Interface\IJoinable;

class OneToManyEntityFormatter extends BaseFormatter
{
    /**
     * @param mixed $value
     * @return array{label: string, value: string}[]|null
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function format(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }
        
        if ($value instanceof Collection === false) {
            throw new CollectionException("Value '{$value}' must be instance of Collection.");
        }
        
        $result = [];
        foreach ($value as $item) {
            if ($item instanceof IJoinable) {
                $result[] = [
                    'value' => $item->getId(),
                    'label' => $item->getJoinableLabel()
                ];
            } else {
                throw new CollectionException("Value '{$item}' must be class that implements IJoinable interface.");
            }
        }
        
        return $result;
    }
}