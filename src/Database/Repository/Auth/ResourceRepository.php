<?php
declare(strict_types=1);

namespace Megio\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Auth\Resource;

/**
 * @method Resource|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Resource|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Resource[] findAll()
 * @method Resource[] findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends EntityRepository<Resource>
 */
class ResourceRepository extends EntityRepository
{
}