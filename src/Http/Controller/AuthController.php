<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

use Saas\Security\Guard\ResourceGuard;
use Saas\Http\Request\Auth\EmailAuthRequest;
use Saas\Http\Request\Auth\RevokeTokenRequest;
use Saas\Security\Permissions\DefaultResource;

class AuthController extends Controller
{
    public function email(EmailAuthRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::AuthRevokeToken])]
    public function revokeToken(RevokeTokenRequest $request): void
    {
    }
}