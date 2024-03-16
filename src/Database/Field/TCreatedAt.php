<?php
declare(strict_types=1);

namespace Megio\Database\Field;

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