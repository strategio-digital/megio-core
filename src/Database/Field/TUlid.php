<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Database\Field;

use Saas\Extension\Doctrine\UlidGenerator;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TUlid
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    protected string $id;
    
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}