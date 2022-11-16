<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Database\Entity\User;

use Saas\Database\Field\TCreatedAt;
use Saas\Database\Field\TUlid;
use Saas\Database\Field\TUpdatedAt;
use Saas\Database\Repository\UserTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: '`fw_user_token`')]
#[ORM\Entity(repositoryClass: UserTokenRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Token
{
    use TUlid;
    use TCreatedAt;
    use TUpdatedAt;
    
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private User $user;
    
    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string $token;
    
    #[ORM\Column(nullable: false)]
    private \DateTime $expiration;
    
    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
    
    /**
     * @param User $user
     * @return Token
     */
    public function setUser(User $user): Token
    {
        $this->user = $user;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
    
    /**
     * @param string $token
     * @return Token
     */
    public function setToken(string $token): Token
    {
        $this->token = $token;
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getExpiration(): \DateTime
    {
        return $this->expiration;
    }
    
    /**
     * @param \DateTime $expiration
     * @return Token
     */
    public function setExpiration(\DateTime $expiration): Token
    {
        $this->expiration = $expiration;
        return $this;
    }
}