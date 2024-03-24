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
    
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    #[ORM\PrePersist]
    public function onPrePersistCreatedAt(): void
    {
        $this->createdAt = new \DateTime();
    }
}