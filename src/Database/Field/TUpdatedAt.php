<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Database\Field;

use Doctrine\ORM\Mapping as ORM;

trait TUpdatedAt
{
    #[ORM\Column]
    private ?\DateTime $updatedAt = null;
    
    public function getUpdatedAt(): ?\DateTime
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