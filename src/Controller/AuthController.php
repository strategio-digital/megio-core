<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Controller;

use Saas\Guard\Attribute\ResourceGuard;
use Saas\Request\Auth\EmailAuthRequest;
use Saas\Request\Auth\RevokeTokenRequest;
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