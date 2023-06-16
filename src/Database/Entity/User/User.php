<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Database\Entity\User;

use Saas\Database\Entity\Role\Role;
use Saas\Database\Field\TCreatedAt;
use Saas\Database\Field\TUlid;
use Saas\Database\Field\TUpdatedAt;
use Saas\Database\Interface\CrudEntity;
use Saas\Database\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Nette\InvalidArgumentException;
use Nette\Security\Passwords;
use Nette\Utils\Validators;

#[ORM\Table(name: '`user`')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements CrudEntity
{
    use TUlid;
    use TCreatedAt;
    use TUpdatedAt;
    
    /** @var string[] */
    public array $visibleFields = ['id', 'createdAt', 'updatedAt', 'email', 'lastLogin'];
    
    #[ORM\Column(length: 64, unique: true, nullable: false)]
    private string $email;
    
    #[ORM\Column(nullable: false)]
    private string $password;
    
    #[ORM\Column(nullable: true)]
    private ?\DateTime $lastLogin = null;
    
    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Role $role = null;
    
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        if (!Validators::isEmail($email)) {
            throw new InvalidArgumentException("E-mail: '{$email}' is not valid.");
        }
        
        $this->email = $email;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    
    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $length = mb_strlen($password);
        
        if ($length < 6 || $length > 32) {
            throw new InvalidArgumentException("Password length is not in range 6 ... 32 chars, {$length} chars given.");
        }
        
        $this->password = (new Passwords(PASSWORD_ARGON2ID))->hash($password);
        return $this;
    }
    
    /**
     * @return \DateTime|null
     */
    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }
    
    /**
     * @return User
     */
    public function setLastLogin(): User
    {
        $this->lastLogin = new \DateTime();
        return $this;
    }
    
    /**
     * @return ?Role
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }
    
    /**
     * @param Role $role
     * @return User
     */
    public function setRole(Role $role): User
    {
        $this->role = $role;
        return $this;
    }
}