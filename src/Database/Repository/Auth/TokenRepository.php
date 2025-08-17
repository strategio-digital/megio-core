<?php
declare(strict_types=1);

namespace Megio\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Auth\Token;

/**
 * @method Token|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Token|NULL findOneBy(array<string, mixed> $criteria, array<string, string>|NULL $orderBy = NULL)
 * @method Token[] findAll()
 * @method Token[] findBy(array<string, mixed> $criteria, array<string, string>|NULL $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<Token>
 */
class TokenRepository extends EntityRepository
{
}