<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Database\Repository\Auth;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<RoleRepository>
 */
class RoleRepository extends EntityRepository
{
}