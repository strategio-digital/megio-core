<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Component\Uid\UuidV7;

class UuidV7Generator extends AbstractIdGenerator
{
    public function generateId(
        EntityManagerInterface $em,
        ?object $entity,
    ): string {
        return UuidV7::generate();
    }
}
