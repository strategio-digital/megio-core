<?php

declare(strict_types=1);

namespace Megio\Translation\Facade\Dto;

readonly class LanguageStatisticsDto
{
    public function __construct(
        public string $posix,
        public string $shortCode,
        public string $name,
        public bool $isDefault,
        public bool $isEnabled,
        public int $total,
        public int $fromSource,
        public int $fromDb,
        public int $deleted,
    ) {}
}
