<?php
declare(strict_types=1);

namespace Megio\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Auth\Role;

/**
 * @method Role|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Role|NULL findOneBy(array<string, mixed> $criteria, array<string, string>|NULL $orderBy = NULL)
 * @method Role[] findAll()
 * @method Role[] findBy(array<string, mixed> $criteria, array<string, string>|NULL $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<Role>
 */
class RoleRepository extends EntityRepository
{
}