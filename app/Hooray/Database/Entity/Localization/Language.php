<?php
declare(strict_types=1);

namespace App\Hooray\Database\Entity\Localization;

use App\Hooray\Database\Entity\Shop;
use App\Hooray\Database\Repository\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Interface\IJoinable;

#[ORM\Table(name: '`language`')]
#[ORM\Entity(repositoryClass: ShopRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Language implements ICrudable, IJoinable
{
    use TId, TCreatedAt, TUpdatedAt;
    
    #[ORM\Column(unique: true)]
    protected string $code;
    
    #[ORM\Column]
    protected string $name;
    
    #[ORM\Column(options: ['default' => false])]
    protected bool $isPrimary = false;
    
    #[ORM\OneToOne(inversedBy: 'language', targetEntity: Shop::class)]
    protected ?Shop $shop = null;
    
    /** @var Collection<int, Translation> */
    #[ORM\OneToMany(mappedBy: 'language', targetEntity: Translation::class, fetch: 'LAZY')]
    protected Collection $translations;
    
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }
    
    public function getCode(): string
    {
        return $this->code;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }
    
    public function getShop(): ?Shop
    {
        return $this->shop;
    }
    
    /** @return Collection<int, Translation> */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }
    
    public function getJoinableLabel(): array
    {
        return [
            'fields' => ['name'],
            'format' => '%s'
        ];
    }
}