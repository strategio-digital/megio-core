<?php
declare(strict_types=1);

namespace App\Database;

use App\Database\Entity\Hooray\Customer\ApprovalPage;
use App\Database\Entity\Hooray\Localization\Language;
use App\Database\Entity\Hooray\Localization\Translation;
use App\Database\Entity\Hooray\Order\Comment;
use App\Database\Entity\Hooray\Order\Order;
use App\Database\Entity\Hooray\Order\Status;
use App\Database\Entity\Hooray\Shop;
use App\Database\Entity\User;
use App\Database\Repository\Hooray\Customer\ApprovalPageRepository;
use App\Database\Repository\Hooray\Localization\LanguageRepository;
use App\Database\Repository\Hooray\Localization\TranslationRepository;
use App\Database\Repository\Hooray\Order\CommentRepository;
use App\Database\Repository\Hooray\Order\OrderRepository;
use App\Database\Repository\Hooray\Order\OrderStatusRepository;
use App\Database\Repository\Hooray\ShopRepository;
use App\Database\Repository\UserRepository;

class EntityManager extends \Megio\Database\EntityManager
{
    public function getUserRepo(): UserRepository
    {
        return $this->getRepository(User::class); // @phpstan-ignore-line
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