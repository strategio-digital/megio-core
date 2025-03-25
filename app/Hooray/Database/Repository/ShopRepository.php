<?php
declare(strict_types=1);

namespace App\Hooray\Database\Repository;

use App\Hooray\Database\Entity\Shop;
use Doctrine\ORM\EntityRepository;

/**
 * @method Shop|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Shop|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method Shop[] findAll()
 * @method Shop[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<ShopRepository>
 */
class ShopRepository extends EntityRepository
{
}