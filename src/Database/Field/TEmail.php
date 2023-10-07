<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Database\Field;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Validators;
use Megio\Database\Entity\EntityException;

trait TEmail
{
    #[ORM\Column(length: 64, unique: true, nullable: false)]
    protected string $email;
    
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     * @return self
     * @throws \Megio\Database\Entity\EntityException
     */
    public function setEmail(string $email): self
    {
        if (!Validators::isEmail($email)) {
            throw new EntityException('E-mail address is not valid');
        }
        
        $this->email = $email;
        return $this;
    }
}