<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Enum;

enum ResourceType: string
{
    case ROUTER = 'router';
    case ROUTER_VIEW = 'router.view';
    case COLLECTION_DATA = 'collection.data';
    case COLLECTION_NAV = 'collection.nav';
    
    public function getResourcesMethodName(): string {
        // Method names in AuthResourceManager
        return match($this) {
            self::ROUTER => 'routerResources',
            self::ROUTER_VIEW => 'routerViewResources',
            self::COLLECTION_DATA => 'collectionDataResources',
            self::COLLECTION_NAV => 'collectionNavResources',
        };
    }
}