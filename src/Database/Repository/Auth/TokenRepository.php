<?php
declare(strict_types=1);

namespace Megio\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;

/**
 * @method \Megio\Database\Entity\Auth\Token|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method \Megio\Database\Entity\Auth\Token|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method \Megio\Database\Entity\Auth\Token[] findAll()
 * @method \Megio\Database\Entity\Auth\Token[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<TokenRepository>
 */
class TokenRepository extends EntityRepository
{
}