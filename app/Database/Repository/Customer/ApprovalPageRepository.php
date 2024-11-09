<?php
declare(strict_types=1);

namespace App\Database\Repository\Customer;

use App\Database\Enum\OrderStatusPurpose;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @method \App\Database\Entity\Customer\ApprovalPage|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method \App\Database\Entity\Customer\ApprovalPage|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method \App\Database\Entity\Customer\ApprovalPage[] findAll()
 * @method \App\Database\Entity\Customer\ApprovalPage[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<ApprovalPageRepository>
 */
class ApprovalPageRepository extends EntityRepository
{
    public function findPageQb(string $orderNumber, string $email): QueryBuilder
    {
        $status = OrderStatusPurpose::READY_FOR_CUSTOMER_APPROVAL;
        
        return $this->createQueryBuilder('page')
            ->select('page')
            ->leftJoin('page.order_', 'order_')
            ->leftJoin('order_.status', 'status')
            ->where('order_.orderNumber = :orderNumber')
            ->andWhere('order_.email = :email')
            ->andWhere('status.purpose = :purpose')
            ->setParameter('orderNumber', $orderNumber)
            ->setParameter('purpose', $status)
            ->setParameter('email', $email);
    }
}