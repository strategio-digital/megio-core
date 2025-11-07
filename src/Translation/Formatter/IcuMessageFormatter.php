<?php

declare(strict_types=1);

namespace Megio\Translation\Formatter;

use Symfony\Component\Translation\Formatter\IntlFormatter;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;

readonly class IcuMessageFormatter implements MessageFormatterInterface
{
    private IntlFormatter $intlFormatter;

    public function __construct()
    {
        $this->intlFormatter = new IntlFormatter();
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function format(
        string $message,
        string $locale,
        array $parameters = [],
    ): string {
        // Use IntlFormatter for ICU MessageFormat support
        return $this->intlFormatter->formatIntl($message, $locale, $parameters);
    }
}
