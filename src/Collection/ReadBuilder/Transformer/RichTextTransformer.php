<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Transformer;

use Megio\Collection\ReadBuilder\Transformer\Base\BaseTransformer;
use Nette\Utils\Strings;

class RichTextTransformer extends BaseTransformer
{
    public function __construct(
        protected int    $max = 100,
        protected bool   $truncate = true,
        protected string $suffix = '...',
        protected bool   $stripTags = true,
        protected bool   $adminPanelOnly = false
    )
    {
        parent::__construct($adminPanelOnly);
    }
    
    public function transform(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }
        
        $value = strip_tags($value);
        
        if (Strings::length($value) > $this->max) {
            if ($this->truncate) {
                $value = Strings::truncate($value, $this->max, $this->suffix);
            } else {
                $value = Strings::substring($value, 0, $this->max) . $this->suffix;
            }
        }
        
        return $value;
    }
}