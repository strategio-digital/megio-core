<?php
declare(strict_types=1);

namespace App\Database\Entity\Hooray\Order;

use App\Database\Repository\Hooray\Order\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Interface\IJoinable;

#[ORM\Table(name: '`orders_comment`')]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Comment implements ICrudable, IJoinable
{
    use TId, TCreatedAt, TUpdatedAt;
    
    #[ORM\Column]
    protected string $source;
    
    #[ORM\Column(type: 'text')]
    protected string $message;
    
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    protected Order $order_;
    
    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }
    
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
    
    public function setOrder(Order $order): self
    {
        $this->order_ = $order;
        return $this;
    }
    
    public function getSource(): string
    {
        return $this->source;
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
    
    
    public function getOrder(): Order
    {
        return $this->order_;
    }
    
    public function getJoinableLabel(): array
    {
        return [
            'fields' => ['message'],
            'format' => '%s'
        ];
    }
}