<?php
declare(strict_types=1);

namespace App\Hooray\Database\Repository\Order;

use App\Hooray\Database\Entity\Order\Status;
use App\Hooray\Database\Enum\OrderStatusPurpose;
use Doctrine\ORM\EntityRepository;

/**
 * @method Status|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Status|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method Status[] findAll()
 * @method Status[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<OrderStatusRepository>
 */
class OrderStatusRepository extends EntityRepository
{
    public function findOutgoingStatus(OrderStatusPurpose $purpose): ?Status
    {
        return $this->findOneBy([
            'purpose' => $purpose,
            'isOutgoing' => true
        ]);
    }
}