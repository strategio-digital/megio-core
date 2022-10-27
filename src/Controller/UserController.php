<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Controller;

use Framework\Guard\Attribute\ResourceGuard;
use Framework\Request\User\CreateRequest;
use Framework\Request\User\EmailLoginRequest;
use Framework\Request\User\ProfileRequest;
use Framework\Request\User\UploadAvatarRequest;
use Framework\Security\Permissions\DefaultResource;

class UserController extends Controller
{
    public function create(CreateRequest $request): void
    {
    }
    
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
}