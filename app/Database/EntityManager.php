<?php
declare(strict_types=1);

namespace App\Database;

use App\Database\Entity\Customer\ApprovalPage;
use App\Database\Entity\Localization\Language;
use App\Database\Entity\Localization\Translation;
use App\Database\Entity\Order\Comment;
use App\Database\Entity\Order\Order;
use App\Database\Entity\Order\Status;
use App\Database\Entity\Shop;
use App\Database\Entity\User;
use App\Database\Repository\Customer\ApprovalPageRepository;
use App\Database\Repository\Localization\LanguageRepository;
use App\Database\Repository\Localization\TranslationRepository;
use App\Database\Repository\Order\CommentRepository;
use App\Database\Repository\Order\OrderRepository;
use App\Database\Repository\Order\OrderStatusRepository;
use App\Database\Repository\ShopRepository;
use App\Database\Repository\UserRepository;
use Megio\Database\Entity\Queue;
use Megio\Database\Repository\QueueRepository;

class EntityManager extends \Megio\Database\EntityManager
{
    public function getUserRepo(): UserRepository
    {
        return $this->getRepository(User::class); // @phpstan-ignore-line
    }
    
    public function getQueueRepo(): QueueRepository
    {
        return $this->getRepository(Queue::class); // @phpstan-ignore-line
    }
    
    public function getOrderRepo(): OrderRepository
    {
        return $this->getRepository(Order::class); // @phpstan-ignore-line
    }
    
    public function getOrderStatusRepo(): OrderStatusRepository
    {
        return $this->getRepository(Status::class); // @phpstan-ignore-line
    }
    
    public function getShopRepo(): ShopRepository
    {
        return $this->getRepository(Shop::class); // @phpstan-ignore-line
    }
    
    public function getCustomerApprovalPage(): ApprovalPageRepository
    {
        return $this->getRepository(ApprovalPage::class); // @phpstan-ignore-line
    }
    
    public function getOrderCommentRepository(): CommentRepository
    {
        return $this->getRepository(Comment::class); // @phpstan-ignore-line
    }
    
    public function getLanguageRepository(): LanguageRepository
    {
        return $this->getRepository(Language::class); // @phpstan-ignore-line
    }
    
    public function getTranslationRepository(): TranslationRepository
    {
        return $this->getRepository(Translation::class); // @phpstan-ignore-line
    }
}