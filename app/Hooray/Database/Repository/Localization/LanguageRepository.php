<?php
declare(strict_types=1);

namespace App\Hooray\Database\Repository\Localization;

use App\Hooray\Database\Entity\Localization\Language;
use Doctrine\ORM\EntityRepository;

/**
 * @method Language|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Language|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method Language[] findAll()
 * @method Language[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<LanguageRepository>
 */
class LanguageRepository extends EntityRepository
{
}