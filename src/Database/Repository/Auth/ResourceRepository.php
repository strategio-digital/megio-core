<?php
declare(strict_types=1);

namespace Megio\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Auth\Resource;

/**
 * @method Resource|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Resource|NULL findOneBy(array<string, mixed> $criteria, array<string, string>|NULL $orderBy = NULL)
 * @method Resource[] findAll()
 * @method Resource[] findBy(array<string, mixed> $criteria, array<string, string>|NULL $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<Resource>
 */
class ResourceRepository extends EntityRepository
{
}