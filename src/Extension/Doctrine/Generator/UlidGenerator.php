<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Component\Uid\Ulid;

class UlidGenerator extends AbstractIdGenerator
{
    public function generateId(EntityManagerInterface $em, $entity): string
    {
        return Ulid::generate();
    }
}