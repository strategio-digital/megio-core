<?php
declare(strict_types=1);

namespace App;

use App\Hooray\Database\Entity\Customer\ApprovalPage;
use App\Hooray\Database\Entity\Localization\Language;
use App\Hooray\Database\Entity\Localization\Translation;
use App\Hooray\Database\Entity\Order\Comment;
use App\Hooray\Database\Entity\Order\Order;
use App\Hooray\Database\Entity\Order\Status;
use App\Hooray\Database\Entity\Shop;
use App\Hooray\Database\Repository\Customer\ApprovalPageRepository;
use App\Hooray\Database\Repository\Localization\LanguageRepository;
use App\Hooray\Database\Repository\Localization\TranslationRepository;
use App\Hooray\Database\Repository\Order\CommentRepository;
use App\Hooray\Database\Repository\Order\OrderRepository;
use App\Hooray\Database\Repository\Order\OrderStatusRepository;
use App\Hooray\Database\Repository\ShopRepository;
use App\User\Database\Entity\User;
use App\User\Database\Repository\UserRepository;

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