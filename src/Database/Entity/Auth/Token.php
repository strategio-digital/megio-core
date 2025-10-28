<?php
declare(strict_types=1);

namespace Megio\Database\Entity\Auth;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Repository\Auth\TokenRepository;

#[ORM\Table(name: '`auth_token`')]
#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Index(fields: ['source', 'sourceId'])]
#[ORM\HasLifecycleCallbacks]
class Token
{
    use TId;
    use TCreatedAt;
    use TUpdatedAt;

    #[ORM\Column(length: 32, nullable: false)]
    private string $source;

    #[ORM\Column(type: Types::GUID)]
    private string $sourceId;

    #[ORM\Column(nullable: false)]
    private DateTime $expiration;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string $token;

    /**
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     */
    public function getExpiration(): DateTime
    {
        return $this->expiration;
    }

    /**
     */
    public function setExpiration(DateTime $expiration): void
    {
        $this->expiration = $expiration;
    }

    /**
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     */
    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    /**
     */
    public function setSourceId(string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }
}
