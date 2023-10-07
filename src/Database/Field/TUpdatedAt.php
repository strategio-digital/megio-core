<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Database\Field;

use Doctrine\ORM\Mapping as ORM;

trait TUpdatedAt
{
    #[ORM\Column(nullable: false)]
    private \DateTime $updatedAt;
    
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    
    #[ORM\PreFlush]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime();
        return $this;
    }
}