<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Guard\Attribute;

use Saas\Security\Permissions\IResource;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class ResourceGuard
{
    /**
     * @param array<int, IResource> $resources
     */
    public function __construct(public array $resources = [])
    {
    }
}