<?php
declare(strict_types=1);

namespace Megio\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;
use Megio\Database\Entity\Auth\Token;

/**
 * @method Token|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Token|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Token[] findAll()
 * @method Token[] findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends EntityRepository<Token>
 */
class TokenRepository extends EntityRepository
{
}