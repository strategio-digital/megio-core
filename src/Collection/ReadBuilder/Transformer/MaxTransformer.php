<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Transformer;

use Megio\Collection\ReadBuilder\Transformer\Base\BaseTransformer;
use Nette\Utils\Strings;

class MaxTransformer extends BaseTransformer
{
    public function __construct(
        protected int  $max,
        protected bool $adminPanelOnly = false
    )
    {
        parent::__construct($adminPanelOnly);
    }
    
    public function transform(mixed $value): mixed
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