<?php
declare(strict_types=1);

namespace App\Database\Entity\Customer;

use App\Database\Entity\Order\Order;
use App\Database\Repository\Customer\ApprovalPageRepository;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Interface\IJoinable;

#[ORM\Table(name: '`approval_page`')]
#[ORM\Entity(repositoryClass: ApprovalPageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ApprovalPage implements ICrudable, IJoinable
{
    use TId, TCreatedAt, TUpdatedAt;
    
    #[ORM\OneToOne(inversedBy: 'approvalPage', targetEntity: Order::class)]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    protected Order $order_;
    
    /** @var array<int, array{image: string, note: string|null}> */
    #[ORM\Column(type: 'json')]
    protected array $items = [];
    
    public function getOrder(): Order
    {
        return $this->order_;
    }
    
    public function setOrder(Order $order): void
    {
        $order->setApprovalPage($this);
        $this->order_ = $order;
    }
    
    
    /**
     * @param array<int, array{image: string, note: string|null}> $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
    
    /**
     * @return array<int, array{image: string, note: string|null}>
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    public function getJoinableLabel(): array
    {
        return [
            'fields' => ['id'],
            'format' => '%s',
        ];
    }
}