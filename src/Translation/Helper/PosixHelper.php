<?php

declare(strict_types=1);

namespace Megio\Translation\Helper;

use function str_replace;
use function substr;

final class PosixHelper
{
    /**
     * Extracts 2-letter short code from POSIX locale
     *
     * Example: "cs_CZ" returns "cs"
     */
    public static function extractShortCode(string $posix): string
    {
        return substr($posix, 0, 2);
    }

    /**
     * Converts POSIX locale to BCP 47 format
     *
     * Example: "cs_CZ" returns "cs-CZ"
     */
    public static function toBcp47(string $posix): string
    {
        return str_replace('_', '-', $posix);
    }
}
