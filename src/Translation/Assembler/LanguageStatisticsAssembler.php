<?php

declare(strict_types=1);

namespace Megio\Translation\Assembler;

use Megio\Database\EntityManager;
use Megio\Translation\Facade\Dto\LanguageStatisticsDto;

final readonly class LanguageStatisticsAssembler
{
    public function __construct(
        private EntityManager $em,
    ) {}

    /**
     * @return LanguageStatisticsDto[]
     */
    public function assemble(): array
    {
        $languages = $this->em->getLanguageRepo()->findAll();
        $statistics = [];

        foreach ($languages as $language) {
            $total = $this->em->getTranslationRepo()->countByLanguage($language);
            $fromSource = $this->em->getTranslationRepo()->countFromSourceByLanguage($language);
            $deleted = $this->em->getTranslationRepo()->countDeletedByLanguage($language);

            $statistics[] = new LanguageStatisticsDto(
                posix: $language->getPosix(),
                shortCode: $language->getShortCode(),
                name: $language->getName(),
                isDefault: $language->isDefault(),
                isEnabled: $language->isEnabled(),
                total: $total,
                fromSource: $fromSource,
                fromDb: $total - $fromSource,
                deleted: $deleted,
            );
        }

        return $statistics;
    }
}
