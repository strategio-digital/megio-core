<?php

declare(strict_types=1);

namespace Megio\Translation\Helper;

use function str_replace;

final class NeonFilenameHelper
{
    /**
     * Regex pattern for matching locale NEON files (e.g., app.locale.cs_CZ.neon)
     */
    public const string LOCALE_REGEX = '/\.locale\.([a-z]{2}_[A-Z]{2})\.neon$/';

    /**
     * Extracts domain name from NEON filename
     *
     * Example: "app.locale.cs_CZ.neon" with posix "cs_CZ" returns "app"
     */
    public static function extractDomain(string $filename, string $posix): string
    {
        return str_replace('.locale.' . $posix . '.neon', '', $filename);
    }
}
