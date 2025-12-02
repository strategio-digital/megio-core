<?php

declare(strict_types=1);

namespace Megio\Database\Repository\Translation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Translation\Language;

/**
 * @extends EntityRepository<Language>
 */
class LanguageRepository extends EntityRepository
{
    public function findOneByPosix(string $posix): ?Language
    {
        return $this->findOneBy(['posix' => $posix]);
    }

    public function findDefault(): ?Language
    {
        return $this->findOneBy(['isDefault' => true]);
    }

    /**
     * @return Collection<int, Language>
     */
    public function findEnabled(): Collection
    {
        $result = $this->findBy(['isEnabled' => true]);
        return new ArrayCollection($result);
    }

    /**
     * @return Collection<int, Language>
     */
    public function findByShortCode(string $shortCode): Collection
    {
        $result = $this->findBy(['shortCode' => $shortCode]);
        return new ArrayCollection($result);
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
