<?php
declare(strict_types=1);

namespace Megio\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;

/**
 * @method \Megio\Database\Entity\Auth\Role|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method \Megio\Database\Entity\Auth\Role|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method \Megio\Database\Entity\Auth\Role[] findAll()
 * @method \Megio\Database\Entity\Auth\Role[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<RoleRepository>
 */
class RoleRepository extends EntityRepository
{
}