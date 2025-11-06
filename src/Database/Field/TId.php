<?php
declare(strict_types=1);

namespace Megio\Database\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Megio\Extension\Doctrine\Generator\UuidV7Generator;

trait TId
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
    private string $id;

    /**
     */
    public function getId(): string
    {
        return $this->id;
    }
}
