<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Enum;

enum ResourceType: string
{
    case ROUTE_NAME = 'route.name';
    case COLLECTION = 'collection';
}