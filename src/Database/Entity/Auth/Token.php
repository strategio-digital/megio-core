<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Entity\Auth;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Saas\Database\Field\TCreatedAt;
use Saas\Database\Field\TId;
use Saas\Database\Field\TUpdatedAt;
use Saas\Database\Repository\Auth\TokenRepository;

#[ORM\Table(name: '`auth_token`')]
#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Index(fields: ['source', 'sourceId'])]
#[ORM\HasLifecycleCallbacks]
class Token
{
    use TId, TCreatedAt, TUpdatedAt;
    
    #[ORM\Column(length: 32, nullable: false)]
    private string $source;
    
    #[ORM\Column(type: Types::GUID)]
    private string $sourceId;
    
    #[ORM\Column(nullable: false)]
    private \DateTime $expiration;
    
    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string $token;
    
    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
    
    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
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
     */
    public function setExpiration(\DateTime $expiration): void
    {
        $this->expiration = $expiration;
    }
    
    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }
    
    /**
     * @param string $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }
    
    /**
     * @return string
     */
    public function getSourceId(): string
    {
        return $this->sourceId;
    }
    
    /**
     * @param string $sourceId
     */
    public function setSourceId(string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }
}