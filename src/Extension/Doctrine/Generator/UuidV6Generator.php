<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Component\Uid\UuidV6;

class UuidV6Generator extends AbstractIdGenerator
{
    public function generateId(EntityManagerInterface $em, object|null $entity): string
    {
        return UuidV6::generate();
    }
}