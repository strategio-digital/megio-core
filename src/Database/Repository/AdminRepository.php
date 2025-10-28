<?php
declare(strict_types=1);

namespace Megio\Database\Repository;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Admin;

/**
 * @method Admin|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Admin|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Admin[] findAll()
 * @method Admin[] findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 *
 * @extends EntityRepository<Admin>
 */
class AdminRepository extends EntityRepository {}
