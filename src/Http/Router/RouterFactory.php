<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Router;

use Saas\Http\Controller\AuthController;
use Saas\Http\Controller\AppController;
use Saas\Http\Controller\UserController;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class RouterFactory extends Router
{
    public function create(): UrlMatcher
    {
        // App & Auth
        $this->add('GET', '/api', [AppController::class, 'index'], [], 'app');
        $this->add('POST', '/api/auth/email', [AuthController::class, 'email'], [], 'auth_email');
        $this->add('POST', '/api/auth/revoke-token', [AuthController::class, 'revokeToken'], [], 'auth_revoke_token');
    
        // User CRUD
        $this->add('POST', '/api/user/show', [UserController::class, 'show'], [], 'user_show_all');
        $this->add('POST', '/api/user/show-one', [UserController::class, 'showOne'], [], 'user_show_one');
        $this->add('POST', '/api/user/create', [UserController::class, 'create'], [], 'user_create');
        $this->add('DELETE', '/api/user/delete', [UserController::class, 'delete'], [], 'user_delete');
        
        // User extra
        $this->add('POST', '/api/user/show-profile', [UserController::class, 'profile'], [], 'user_profile');
        $this->add('POST', '/api/user/upload-avatar', [UserController::class, 'uploadAvatar'], [], 'user_upload_avatar');
    
        return parent::create();
    }
}