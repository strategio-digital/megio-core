<?php

use Saas\Helper\Router;
use Saas\Http\Controller\AppController;
use Saas\Http\Request\Auth as Auth;
use Saas\Http\Request\Collection as Collection;
use Saas\Http\Request\Admin as Admin;
use Saas\Http\Request\Resource as Resource;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    // App
    $routes->add(Router::ROUTE_APP, '/app{uri}')
        ->methods(['GET'])
        ->controller([AppController::class, 'app'])
        ->requirements(['uri' => '.*'])
        ->options(['auth' => false]);
    
    // Api overview
    $routes->add(Router::ROUTE_API, '/api')
        ->methods(['GET'])
        ->controller([AppController::class, 'api'])
        ->options(['auth' => false]);
    
    // Auth
    $auth = $routes->collection('saas.auth.')->prefix('/saas/auth');
    
    $auth->add('email', '/email')
        ->methods(['POST'])
        ->controller(Auth\EmailAuthRequest::class)
        ->options(['auth' => false]);
    
    $auth->add('revoke-token', '/revoke-token')
        ->methods(['POST'])
        ->controller(Auth\RevokeTokenRequest::class)
        ->options(['inResources' => false]);
    
    // Admin
    $admin = $routes->collection('saas.admin.')->prefix('/saas/admin');
    
    $admin->add('profile', '/profile')
        ->methods(['POST'])
        ->controller(Admin\ProfileRequest::class)
        ->options(['inResources' => false]);;
    
    $admin->add('avatar', '/avatar')
        ->methods(['POST'])
        ->controller(Admin\UploadAvatarRequest::class)
        ->options(['inResources' => false]);;
    
    // Collections
    $collection = $routes->collection(Router::ROUTE_COLLECTION_PREFIX)->prefix('/saas/collections');
    $collection->add('show', '/show')->methods(['POST'])->controller(Collection\ShowRequest::class);
    $collection->add('show-one', '/show-one')->methods(['POST'])->controller(Collection\ShowOneRequest::class);
    $collection->add('create', '/create')->methods(['POST'])->controller(Collection\CreateRequest::class);
    $collection->add('delete', '/delete')->methods(['DELETE'])->controller(Collection\DeleteRequest::class);
    $collection->add('update', '/update')->methods(['PATCH'])->controller(Collection\UpdateRequest::class);
    
    // Collections navbar
    $routes->add(Router::ROUTE_META_NAVBAR, '/saas/collections/navbar')
        ->methods(['POST'])
        ->controller(Collection\NavbarRequest::class);
    
    // Resources
    $resources = $routes->collection('saas.resources.')->prefix('/saas/resources');
    
    $resources->add('show', '/show')
        ->methods(['POST'])
        ->controller(Resource\ShowAllRequest::class)
        ->options(['inResources' => false]);
    
    $resources->add('update', '/update')
        ->methods(['POST'])
        ->controller(Resource\UpdateResourceRequest::class)
        ->options(['inResources' => false]);
    
    $resources->add('update.role', '/update-role')
        ->methods(['POST'])
        ->controller(Resource\UpdateRoleRequest::class)
        ->options(['inResources' => false]);
    
    $resources->add('delete.role', '/delete-role')
        ->methods(['DELETE'])
        ->controller(Resource\DeleteRoleRequest::class)
        ->options(['inResources' => false]);
};