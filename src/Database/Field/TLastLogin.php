<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Database\Field;

use Doctrine\ORM\Mapping as ORM;

trait TLastLogin
{
    #[ORM\Column(nullable: true)]
    protected ?\DateTime $lastLogin = null;
    
    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }
    
    public function setLastLogin(): self
    {
        $this->lastLogin = new \DateTime();
        return $this;
    }
}