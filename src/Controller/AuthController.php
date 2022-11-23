<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Controller;

use Saas\Request\Auth\EmailAuthRequest;

class AuthController extends Controller
{
    public function email(EmailAuthRequest $request): void
    {
    }
}