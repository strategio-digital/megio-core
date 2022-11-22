<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Router;

use Saas\Controller\HomeController;
use Saas\Controller\UserController;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class RouterFactory extends Router
{
    public function create(): UrlMatcher
    {
        $this->add('GET', '/', [HomeController::class, 'index'], [], 'home');
    
        // User CRUD
        $this->add('POST', '/user/show-all', [UserController::class, 'showAll'], [], 'user_show_all');
        $this->add('POST', '/user/show-one', [UserController::class, 'showOne'], [], 'user_show_one');
        $this->add('POST', '/user/create', [UserController::class, 'create'], [], 'user_create');
        $this->add('DELETE', '/user/delete', [UserController::class, 'delete'], [], 'user_delete');
    
        // User extended
        $this->add('POST', '/user/show-profile', [UserController::class, 'profile'], [], 'user_profile');
        $this->add('POST', '/user/upload-avatar', [UserController::class, 'uploadAvatar'], [], 'user_upload_avatar');
        $this->add('POST', '/user/login/email', [UserController::class, 'loginByEmail'], [], 'user_login_email');
    
        return parent::create();
    }
}