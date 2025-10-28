<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\Formatter\Base\BaseFormatter;
use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;
use Nette\Utils\Strings;

class MaxFormatter extends BaseFormatter
{
    public function __construct(protected int $max, protected ?ShowOnlyOn $showOnlyOn = null)
    {
        parent::__construct($showOnlyOn);
    }

    public function format(mixed $value, string $key): mixed
    {
        if (!is_string($value) && !is_array($value)) {
            return $value;
        }

        if (is_array($value) && count($value) > $this->max) {
            $newArray = [];
            for ($i = 0; $i < $this->max; $i++) {
                $newArray[] = $value[$i];
            }
            return $newArray;
        }

        if (is_string($value) && Strings::length($value) > $this->max) {
            return Strings::substring($value, 0, $this->max);
        }

        return $value;
    }
}
