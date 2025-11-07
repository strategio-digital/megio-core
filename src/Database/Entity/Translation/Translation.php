<?php

declare(strict_types=1);

namespace Megio\Database\Entity\Translation;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Repository\Translation\TranslationRepository;

#[ORM\Entity(repositoryClass: TranslationRepository::class)]
#[ORM\Table(name: 'language_translation')]
#[ORM\UniqueConstraint(columns: ['key', 'domain', 'language_id'])]
#[ORM\Index(columns: ['domain'])]
#[ORM\Index(columns: ['language_id'])]
#[ORM\Index(columns: ['is_deleted'])]
#[ORM\HasLifecycleCallbacks]
class Translation
{
    use TId;
    use TCreatedAt;
    use TUpdatedAt;

    #[ORM\Column(type: Types::STRING)]
    private string $key;

    #[ORM\Column(length: 50)]
    private string $domain;

    #[ORM\ManyToOne(targetEntity: Language::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Language $language;

    #[ORM\Column(type: Types::TEXT)]
    private string $value;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isFromSource = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDeleted = false;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function isFromSource(): bool
    {
        return $this->isFromSource;
    }

    public function setIsFromSource(bool $isFromSource): void
    {
        $this->isFromSource = $isFromSource;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }
}
