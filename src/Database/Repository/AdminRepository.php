<?php
declare(strict_types=1);

namespace Megio\Database\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @method \Megio\Database\Entity\Admin|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method \Megio\Database\Entity\Admin|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method \Megio\Database\Entity\Admin[] findAll()
 * @method \Megio\Database\Entity\Admin[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<AdminRepository>
 */
class AdminRepository extends EntityRepository
{
}