<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Enum;

enum ResourceType: string
{
    case ROUTE = 'route';
    case COLLECTION = 'collection';
    case COLLECTION_NAV = 'collection.nav';
    case CUSTOM = 'custom';
}