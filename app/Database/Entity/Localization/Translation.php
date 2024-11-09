<?php
declare(strict_types=1);

namespace App\Database\Entity\Localization;

use App\Database\Repository\Localization\TranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Interface\IJoinable;

#[ORM\Table(name: '`language_translation`')]
#[ORM\Entity(repositoryClass: TranslationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(columns: ['key', 'language_id'])]
class Translation implements ICrudable, IJoinable
{
    use TId, TCreatedAt, TUpdatedAt;
    
    #[ORM\Column]
    protected string $key;
    
    #[ORM\Column]
    protected string $value;
    
    #[ORM\ManyToOne(targetEntity: Language::class, inversedBy: 'translations')]
    protected Language $language;
    
    public function getKey(): string
    {
        return $this->key;
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function getLanguage(): Language
    {
        return $this->language;
    }
    
    public function getJoinableLabel(): array
    {
        return [
            'fields' => ['value'],
            'format' => '%s'
        ];
    }
}