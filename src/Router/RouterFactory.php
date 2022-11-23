<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Router;

use Saas\Controller\AuthController;
use Saas\Controller\HomeController;
use Saas\Controller\UserController;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class RouterFactory extends Router
{
    public function create(): UrlMatcher
    {
        // App & Auth
        $this->add('GET', '/', [HomeController::class, 'index'], [], 'app');
        $this->add('POST', '/auth/email', [AuthController::class, 'email'], [], 'auth_email');
    
        // User CRUD
        $this->add('POST', '/user/show', [UserController::class, 'show'], [], 'user_show_all');
        $this->add('POST', '/user/show-one', [UserController::class, 'showOne'], [], 'user_show_one');
        $this->add('POST', '/user/create', [UserController::class, 'create'], [], 'user_create');
        $this->add('DELETE', '/user/delete', [UserController::class, 'delete'], [], 'user_delete');
        
        // User extra
        $this->add('POST', '/user/revoke', [UserController::class, 'revoke'], [], 'user_revoke');
        $this->add('POST', '/user/show-profile', [UserController::class, 'profile'], [], 'user_profile');
        $this->add('POST', '/user/upload-avatar', [UserController::class, 'uploadAvatar'], [], 'user_upload_avatar');
    
        return parent::create();
    }
}