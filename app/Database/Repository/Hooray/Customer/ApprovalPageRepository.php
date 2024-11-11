<?php
declare(strict_types=1);

namespace App\Database\Repository\Hooray\Customer;

use App\Database\Entity\Hooray\Customer\ApprovalPage;
use App\Database\Enum\Hooray\OrderStatusPurpose;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @method ApprovalPage|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method ApprovalPage|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method ApprovalPage[] findAll()
 * @method ApprovalPage[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
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