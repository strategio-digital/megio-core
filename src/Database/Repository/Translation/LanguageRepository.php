<?php

declare(strict_types=1);

namespace Megio\Database\Repository\Translation;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Translation\Language;

/**
 * @extends EntityRepository<Language>
 */
class LanguageRepository extends EntityRepository
{
    public function findByCode(string $code): ?Language
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function findDefault(): ?Language
    {
        return $this->findOneBy(['isDefault' => true]);
    }

    /**
     * @return Language[]
     */
    public function findEnabled(): array
    {
        return $this->findBy(['isEnabled' => true]);
    }

    public function unsetAllDefaults(): void
    {
        $qb = $this->createQueryBuilder('l');
        $qb->update(Language::class, 'l')
            ->set('l.isDefault', ':false')
            ->setParameter('false', false)
            ->getQuery()
            ->execute();
    }
}
