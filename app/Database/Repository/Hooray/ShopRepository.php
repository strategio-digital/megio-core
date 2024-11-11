<?php
declare(strict_types=1);

namespace App\Database\Repository\Hooray;

use App\Database\Entity\Hooray\Shop;
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