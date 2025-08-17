<?php
declare(strict_types=1);

namespace Megio\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Auth\Role;

/**
 * @method Role|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Role|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Role[] findAll()
 * @method Role[] findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends EntityRepository<Role>
 */
class RoleRepository extends EntityRepository
{
}