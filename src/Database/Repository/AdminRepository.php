<?php
declare(strict_types=1);

namespace Megio\Database\Repository;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Admin;

/**
 * @method Admin|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Admin|NULL findOneBy(array<string, mixed> $criteria, array<string, string>|NULL $orderBy = NULL)
 * @method Admin[] findAll()
 * @method Admin[] findBy(array<string, mixed> $criteria, array<string, string>|NULL $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<Admin>
 */
class AdminRepository extends EntityRepository
{
}