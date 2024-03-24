<?php
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
    
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    
    #[ORM\PreFlush]
    public function onPreFlushUpdatedAt(): void
    {
        $this->updatedAt = new \DateTime();
    }
}