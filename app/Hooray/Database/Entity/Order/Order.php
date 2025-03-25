<?php
declare(strict_types=1);

namespace App\Hooray\Database\Entity\Order;

use App\Hooray\Database\Entity\Customer\ApprovalPage;
use App\Hooray\Database\Entity\Shop;
use App\Hooray\Database\Repository\Order\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Interface\IJoinable;

#[ORM\Table(name: '`orders`')]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Index(fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
#
class Order implements ICrudable, IJoinable
{
    use TId, TCreatedAt, TUpdatedAt;
    
    #[ORM\Column(unique: true)]
    protected string $orderNumber;
    
    #[ORM\Column(nullable: true)]
    protected ?int $woocommerceId = null;
    
    #[ORM\Column(options: ['default' => ''])]
    protected string $email;
    
    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(name: 'status_id', referencedColumnName: 'id')]
    protected ?Status $status = null;
    
    #[ORM\ManyToOne(targetEntity: Shop::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(name: 'shop_id', referencedColumnName: 'id')]
    protected ?Shop $shop = null;
    
    #[ORM\OneToOne(mappedBy: 'order_', targetEntity: ApprovalPage::class)]
    protected ?ApprovalPage $approvalPage = null;
    
    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'order_', targetEntity: Comment::class, fetch: 'LAZY')]
    protected Collection $comments;
    
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }
    
    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }
    
    public function setWoocommerceId(?int $woocommerceId): Order
    {
        $this->woocommerceId = $woocommerceId;
        return $this;
    }
    
    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }
    
    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;
        return $this;
    }
    
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    
    public function setApprovalPage(?ApprovalPage $approvalPage): self
    {
        $this->approvalPage = $approvalPage;
        return $this;
    }
    
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setOrder($this);
        }
        
        return $this;
    }
    
    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }
    
    public function getWoocommerceId(): ?int
    {
        return $this->woocommerceId;
    }
    
    public function getStatus(): ?Status
    {
        return $this->status;
    }
    
    public function getShop(): ?Shop
    {
        return $this->shop;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function getApprovalPage(): ?ApprovalPage
    {
        return $this->approvalPage;
    }
    
    public function getJoinableLabel(): array
    {
        return [
            'fields' => ['orderNumber'],
            'format' => '%s'
        ];
    }
}