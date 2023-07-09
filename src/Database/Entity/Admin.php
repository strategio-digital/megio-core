<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nette\Security\Passwords;
use Nette\Utils\Validators;
use Saas\Database\Field\TCreatedAt;
use Saas\Database\Field\TId;
use Saas\Database\Field\TUpdatedAt;
use Saas\Database\Interface\Crud;
use Saas\Database\Interface\AuthUser;
use Saas\Database\Repository\AdminRepository;

#[ORM\Table(name: '`admin`')]
#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Admin implements Crud, AuthUser
{
    use TId;
    use TCreatedAt;
    use TUpdatedAt;
    
    /** @var string[] */
    public array $invisibleFields = ['id', 'updatedAt'];
    
    /** @var string[] */
    public array $showAllFields = ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    
    /** @var string[] */
    public array $showOneFields = ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    
    #[ORM\Column(length: 64, unique: true, nullable: false)]
    protected string $email;
    
    #[ORM\Column(nullable: false)]
    protected string $password;
    
    #[ORM\Column(nullable: true)]
    protected ?\DateTime $lastLogin = null;
    
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
    
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     * @return $this
     * @throws \Saas\Database\Entity\EntityException
     */
    public function setEmail(string $email): self
    {
        if (!Validators::isEmail($email)) {
            throw new EntityException('E-mail address is not valid');
        }
        
        $this->email = $email;
        return $this;
    }
    
    #[ORM\PrePersist]
    public function setLastLogin(): AuthUser
    {
        $this->lastLogin = new \DateTime();
        return $this;
    }
}