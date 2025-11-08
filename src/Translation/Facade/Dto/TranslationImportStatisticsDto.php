<?php

declare(strict_types=1);

namespace Megio\Translation\Facade\Dto;

readonly class TranslationImportStatisticsDto
{
    public function __construct(
        public int $imported,
        public int $updated,
        public int $unchanged,
        public int $deleted,
    ) {}
}
