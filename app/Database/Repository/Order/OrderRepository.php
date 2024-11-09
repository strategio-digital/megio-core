<?php
declare(strict_types=1);

namespace App\Database\Repository\Order;

use Doctrine\ORM\EntityRepository;

/**
 * @method \App\Database\Entity\Order\Order|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method \App\Database\Entity\Order\Order|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method \App\Database\Entity\Order\Order[] findAll()
 * @method \App\Database\Entity\Order\Order[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<OrderRepository>
 */
class OrderRepository extends EntityRepository
{
}