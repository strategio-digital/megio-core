<?php
declare(strict_types=1);

namespace Megio\Database\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Megio\Extension\Doctrine\Generator\UuidV6Generator;

trait TId
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidV6Generator::class)]
    protected string $id;

    /**
     */
    public function getId(): string
    {
        return $this->id;
    }
}
