<?php
declare(strict_types=1);

namespace Megio\Database\Enum;

enum ResourceType: string
{
    case ROUTER = 'router';
    case VUE_ROUTER = 'vue.router';
    case COLLECTION_DATA = 'collection.data';
    case COLLECTION_NAV = 'collection.nav';
    
    public function getResourcesMethodName(): string {
        // Method names in AuthResourceManager
        return match($this) {
            self::ROUTER => 'routerResources',
            self::VUE_ROUTER => 'routerViewResources',
            self::COLLECTION_DATA => 'collectionDataResources',
            self::COLLECTION_NAV => 'collectionNavResources',
        };
    }
}