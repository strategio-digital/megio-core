<?php
declare(strict_types=1);

namespace App\Database\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @method \App\Database\Entity\Shop|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method \App\Database\Entity\Shop|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method \App\Database\Entity\Shop[] findAll()
 * @method \App\Database\Entity\Shop[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<ShopRepository>
 */
class ShopRepository extends EntityRepository
{
}