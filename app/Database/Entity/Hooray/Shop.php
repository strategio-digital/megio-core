<?php
declare(strict_types=1);

namespace App\Database\Entity\Hooray;

use App\Database\Entity\Hooray\Localization\Language;
use App\Database\Entity\Hooray\Order\Order;
use App\Database\Enum\Hooray\Api;
use App\Database\Repository\Hooray\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Interface\IJoinable;

#[ORM\Table(name: '`shop`')]
#[ORM\Entity(repositoryClass: ShopRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Shop implements ICrudable, IJoinable
{
    use TId, TCreatedAt, TUpdatedAt;
    
    #[ORM\Column]
    protected string $name;
    
    #[ORM\Column(unique: true)]
    protected string $code;
    
    #[ORM\Column]
    protected string $prefix;
    
    #[ORM\Column(type: 'string', nullable: true, enumType: Api::class)]
    protected ?Api $apiClient = null;
    
    #[ORM\Column(nullable: true)]
    protected ?string $woocommerceUrl = null;
    
    #[ORM\Column(nullable: true)]
    protected ?string $woocommerceConsumerKey = null;
    
    #[ORM\Column(nullable: true)]
    protected ?string $woocommerceConsumerSecret = null;
    
    #[ORM\OneToOne(inversedBy: 'shop', targetEntity: Language::class)]
    protected ?Language $language = null;
    
    /** @var Collection<int, Order> */
    #[ORM\OneToMany(mappedBy: 'shop', targetEntity: Order::class, fetch: 'LAZY')]
    protected Collection $orders;
    
    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getCode(): string
    {
        return $this->code;
    }
    
    public function getPrefix(): string
    {
        return $this->prefix;
    }
    
    public function getApiClient(): ?Api
    {
        return $this->apiClient;
    }
    
    public function getLanguage(): ?Language
    {
        return $this->language;
    }
    
    public function getWoocommerceUrl(): ?string
    {
        return $this->woocommerceUrl;
    }
    
    public function getWoocommerceConsumerKey(): ?string
    {
        return $this->woocommerceConsumerKey;
    }
    
    public function getWoocommerceConsumerSecret(): ?string
    {
        return $this->woocommerceConsumerSecret;
    }
    
    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }
    
    public function getJoinableLabel(): array
    {
        return [
            'fields' => ['name'],
            'format' => '%s'
        ];
    }
}