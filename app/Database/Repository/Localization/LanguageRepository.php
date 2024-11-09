<?php
declare(strict_types=1);

namespace App\Database\Repository\Localization;

use Doctrine\ORM\EntityRepository;

/**
 * @method \App\Database\Entity\Localization\Language|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method \App\Database\Entity\Localization\Language|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method \App\Database\Entity\Localization\Language[] findAll()
 * @method \App\Database\Entity\Localization\Language[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<LanguageRepository>
 */
class LanguageRepository extends EntityRepository
{
}