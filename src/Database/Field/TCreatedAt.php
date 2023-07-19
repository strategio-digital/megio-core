<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Field;

use Doctrine\ORM\Mapping as ORM;

trait TCreatedAt
{
    #[ORM\Column(nullable: false)]
    private \DateTime $createdAt;
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime();
        return $this;
    }
}