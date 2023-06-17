<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

use Saas\Http\Request\Collection\Meta\NavbarRequest;
use Saas\Security\Guard\ResourceGuard;
use Saas\Security\Permissions\DefaultResource;

class SaasController extends Controller
{
    #[ResourceGuard([DefaultResource::SaasCollectionMetaNavbar])]
    public function navbar(NavbarRequest $request): void
    {
    }
}