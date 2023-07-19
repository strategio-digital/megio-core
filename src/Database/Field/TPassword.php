<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Field;

use Doctrine\ORM\Mapping as ORM;
use Nette\Security\Passwords;
use Saas\Database\Entity\EntityException;

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
     * @throws \Saas\Database\Entity\EntityException
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