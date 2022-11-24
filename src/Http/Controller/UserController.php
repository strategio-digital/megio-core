<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

use Saas\Security\Guard\ResourceGuard;
use Saas\Http\Request\User\CreateRequest;
use Saas\Http\Request\User\DeleteRequest;
use Saas\Http\Request\User\ProfileRequest;
use Saas\Http\Request\User\ShowRequest;
use Saas\Http\Request\User\ShowOneRequest;
use Saas\Http\Request\User\UploadAvatarRequest;
use Saas\Security\Permissions\DefaultResource;

class UserController extends Controller
{
    #[ResourceGuard([DefaultResource::UserShow])]
    public function show(ShowRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::UserShowOne])]
    public function showOne(ShowOneRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::UserCreate])]
    public function create(CreateRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::UserDelete])]
    public function delete(DeleteRequest $request): void
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
}