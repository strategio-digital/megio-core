<?php

declare(strict_types=1);

namespace Megio\Database\Repository\Translation;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Translation\Language;
use Megio\Database\Entity\Translation\Translation;

/**
 * @extends EntityRepository<Translation>
 */
class TranslationRepository extends EntityRepository
{
    public function findByKeyDomainAndLanguage(
        string $key,
        string $domain,
        Language $language,
    ): ?Translation {
        return $this->findOneBy([
            'key' => $key,
            'domain' => $domain,
            'language' => $language,
        ]);
    }

    /**
     * @return Translation[]
     */
    public function findSourceTranslations(bool $includeDeleted = false): array
    {
        $criteria = ['isFromSource' => true];

        if ($includeDeleted === false) {
            $criteria['isDeleted'] = false;
        }

        return $this->findBy($criteria);
    }

    /**
     * @return Translation[]
     */
    public function findByLanguageCode(
        string $code,
        bool $includeDeleted = false,
    ): array {
        $qb = $this->createQueryBuilder('t')
            ->join('t.language', 'l')
            ->where('l.code = :code')
            ->setParameter('code', $code);

        if ($includeDeleted === false) {
            $qb->andWhere('t.isDeleted = :deleted')
                ->setParameter('deleted', false);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Translation[]
     */
    public function findByDomain(string $domain): array
    {
        return $this->findBy(
            [
                'domain' => $domain,
                'isDeleted' => false,
            ],
        );
    }

    public function countByLanguage(Language $language): int
    {
        return $this->count(['language' => $language]);
    }

    public function countFromSourceByLanguage(Language $language): int
    {
        return $this->count([
            'language' => $language,
            'isFromSource' => true,
        ]);
    }

    public function countDeletedByLanguage(Language $language): int
    {
        return $this->count([
            'language' => $language,
            'isDeleted' => true,
        ]);
    }
}
