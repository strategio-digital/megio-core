<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Router;

use Framework\Controller\HomeController;
use Framework\Controller\UserController;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class RouterFactory extends Router
{
    public function create(): UrlMatcher
    {
        $this->add('GET', '/', [HomeController::class, 'index'], [], 'home');
        $this->add('POST', '/user/create', [UserController::class, 'create'], [], 'user_create');
        $this->add('POST', '/user/login/email', [UserController::class, 'loginByEmail'], [], 'user_login_email');
        
        $this->add('GET', '/user/profile', [UserController::class, 'profile'], [], 'user_profile');
        $this->add('POST', '/user/upload-avatar', [UserController::class, 'uploadAvatar'], [], 'user_upload_avatar');
    
        return parent::create();
    }
}