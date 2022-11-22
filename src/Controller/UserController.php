<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Controller;

use Saas\Guard\Attribute\ResourceGuard;
use Saas\Request\User\CreateRequest;
use Saas\Request\User\DeleteRequest;
use Saas\Request\User\EmailLoginRequest;
use Saas\Request\User\ProfileRequest;
use Saas\Request\User\ShowAllRequest;
use Saas\Request\User\ShowOneRequest;
use Saas\Request\User\UploadAvatarRequest;
use Saas\Security\Permissions\DefaultResource;

class UserController extends Controller
{
    public function loginByEmail(EmailLoginRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::UserProfileAction])]
    public function profile(ProfileRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::UserUploadAvatarAction])]
    public function uploadAvatar(UploadAvatarRequest $request): void
    {
    }
    
    ##[ResourceGuard([DefaultResource::UserShowAll])]
    public function showAll(ShowAllRequest $request): void
    {
    }
    
    ##[ResourceGuard([DefaultResource::UserShowOne])]
    public function showOne(ShowOneRequest $request): void
    {
    }
    
    ##[ResourceGuard([DefaultResource::UserCreate])]
    public function create(CreateRequest $request): void
    {
    }
    
    ##[ResourceGuard([DefaultResource::UserDelete])]
    public function delete(DeleteRequest $request): void
    {
    }
}