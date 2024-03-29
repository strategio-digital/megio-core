<?php
declare(strict_types=1);

namespace Megio\Database\Field;

use Doctrine\ORM\Mapping as ORM;
use Nette\Security\Passwords;
use Megio\Database\Entity\EntityException;

trait TPassword
{
    #[ORM\Column(nullable: false)]
    protected string $password;
    
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    
    /**
     * @param string $password
     * @return self
     * @throws \Megio\Database\Entity\EntityException
     */
    public function setPassword(string $password): self
    {
        $length = mb_strlen($password);
        
        if ($length < 6 || $length > 32) {
            throw new EntityException("Password length is not in range 6 ... 32 chars, {$length} chars given.");
        }
        
        $this->password = (new Passwords(PASSWORD_ARGON2ID))->hash($password);
        return $this;
    }
}