<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\Formatter\Base\BaseFormatter;
use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;
use Nette\Utils\Strings;

class RichTextFormatter extends BaseFormatter
{
    public function __construct(
        protected int         $max = 100,
        protected bool        $truncate = false,
        protected string      $suffix = '...',
        protected bool        $stripTags = true,
        protected ?ShowOnlyOn $showOnlyOn = null,
    ) {
        parent::__construct($showOnlyOn);
    }

    public function format(mixed $value, string $key): mixed
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
