<?php
declare(strict_types=1);

namespace App\Database\Repository\Hooray\Localization;

use App\Database\Entity\Hooray\Localization\Language;
use App\Database\Entity\Hooray\Localization\Translation;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Nette\Utils\Strings;

/**
 * @method Translation|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Translation|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method Translation[] findAll()
 * @method Translation[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<TranslationRepository>
 */
class TranslationRepository extends EntityRepository
{
    /**
     * @return array<string, string>
     */
    public function findByLanguage(Language $language): array
    {
        /** @var array{key: string, value: string}[] $rows */
        $rows = $this->createQueryBuilder('t')
            ->select('t.key, t.value')
            ->join('t.language', 'l')
            ->where('l.id = :language_id')
            ->setParameter('language_id', $language->getId())
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        
        $items = [];
        foreach ($rows as $row) {
            $key = Strings::trim($row['key']);
            $items[$key] = $row['value'];
        }
        
        return $items;
    }
}