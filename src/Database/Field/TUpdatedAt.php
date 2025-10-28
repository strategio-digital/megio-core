<?php
declare(strict_types=1);

namespace Megio\Database\Field;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait TUpdatedAt
{
    #[ORM\Column(nullable: false)]
    private DateTime $updatedAt;

    private bool $setterUpdatedAtCalled = false;

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->setterUpdatedAtCalled = true;
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PreFlush]
    public function onPreFlushUpdatedAt(): void
    {
        if (!$this->setterUpdatedAtCalled) {
            $this->updatedAt = new DateTime();
        }
    }
}
