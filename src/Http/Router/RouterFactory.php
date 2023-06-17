<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Router;

use Saas\Http\Controller\AuthController;
use Saas\Http\Controller\AppController;
use Saas\Http\Controller\SaasController;
use Saas\Http\Controller\SaasCrudController;
use Saas\Http\Controller\UserController;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class RouterFactory extends Router
{
    public function create(): UrlMatcher
    {
        // App & Auth
        $this->add('GET', '/admin{uri}', [AppController::class, 'app'], [], self::APP, ['uri' => '.*']);
        $this->add('GET', '/api', [AppController::class, 'api'], [], self::API);
        
        $this->add('POST', '/api/auth/email', [AuthController::class, 'email'], [], 'auth.email');
        $this->add('POST', '/api/auth/revoke-token', [AuthController::class, 'revokeToken'], [], 'auth.revoke-token');
        
        // User extra
        $this->add('POST', '/api/user/show-profile', [UserController::class, 'profile'], [], 'user.profile');
        $this->add('POST', '/api/user/upload-avatar', [UserController::class, 'uploadAvatar'], [], 'user.upload-avatar');
        
        // Saas collections CRUD
        $this->add('POST', '/api/saas/crud/show', [SaasCrudController::class, 'show'], [], 'saas.crud.show');
        $this->add('POST', '/api/saas/crud/show-one', [SaasCrudController::class, 'showOne'], [], 'saas.crud.show-one');
        $this->add('POST', '/api/saas/crud/create', [SaasCrudController::class, 'create'], [], 'saas.crud.create');
        $this->add('DELETE', '/api/saas/crud/delete', [SaasCrudController::class, 'delete'], [], 'saas.crud.delete');
        $this->add('PATCH', '/api/saas/crud/update', [SaasCrudController::class, 'update'], [], 'saas.crud.update');
        
        // Saas collections meta
        $this->add('POST', '/api/saas/meta/navbar', [SaasController::class, 'navbar'], [], 'saas.meta.navbar');
        
        return parent::create();
    }
}