<?php
declare(strict_types=1);

namespace Megio\Database\Enum;

enum ResourceType: string
{
    case ROUTER = 'router';
    case ROUTER_VIEW = 'router.view';
    case COLLECTION_RECIPE = 'collection.recipe';
    case COLLECTION_NAV = 'collection.nav';
    
    public function getResourcesMethodName(): string {
        // Method names in AuthResourceManager
        return match($this) {
            self::ROUTER => 'routerResources',
            self::ROUTER_VIEW => 'routerViewResources',
            self::COLLECTION_RECIPE => 'collectionDataResources',
            self::COLLECTION_NAV => 'collectionNavResources',
        };
    }
}