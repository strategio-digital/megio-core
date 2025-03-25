<?php
declare(strict_types=1);

namespace App\Hooray\Database\Entity\Order;

use App\Hooray\Database\Enum\OrderStatusPurpose;
use App\Hooray\Database\Repository\Order\OrderStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Interface\IJoinable;

#[ORM\Table(name: '`orders_status`')]
#[ORM\Entity(repositoryClass: OrderStatusRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Status implements ICrudable, IJoinable
{
    use TId, TCreatedAt, TUpdatedAt;
    
    #[ORM\Column(unique: true)]
    protected string $key;
    
    #[ORM\Column(unique: true, nullable: true)]
    public ?int $baselinkerId = null;
    
    #[ORM\Column]
    protected string $cuteName;
    
    #[ORM\Column(options: ['default' => false])]
    protected bool $isOutgoing = false;
    
    #[ORM\Column(nullable: true, enumType: OrderStatusPurpose::class)]
    protected ?OrderStatusPurpose $purpose = null;
    
    /** @var Collection<int, Order> */
    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Order::class, fetch: 'LAZY')]
    protected Collection $orders;
    
    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }
    
    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }
    
    public function setCuteName(string $cuteName): self
    {
        $this->cuteName = $cuteName;
        return $this;
    }
    
    public function setIsOutgoing(bool $isOutgoing): Status
    {
        $this->isOutgoing = $isOutgoing;
        return $this;
    }
    
    public function setBaselinkerId(?int $baselinkerId): self
    {
        $this->baselinkerId = $baselinkerId;
        return $this;
    }
    
    public function getKey(): string
    {
        return $this->key;
    }
    
    public function getCuteName(): string
    {
        return $this->cuteName;
    }
    
    public function getIsOutgoing(): bool
    {
        return $this->isOutgoing;
    }
    
    public function getBaselinkerId(): ?int
    {
        return $this->baselinkerId;
    }
    
    public function getPurpose(): ?OrderStatusPurpose
    {
        return $this->purpose;
    }
    
    /** @return Collection<int, Order> */
    public function getOrders(): Collection
    {
        return $this->orders;
    }
    
    public function getJoinableLabel(): array
    {
        return [
            'fields' => ['cuteName'],
            'format' => '%s'
        ];
    }
}