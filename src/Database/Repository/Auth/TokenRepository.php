<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<TokenRepository>
 */
class TokenRepository extends EntityRepository
{
}