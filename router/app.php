<?php

use Saas\Helper\Router;
use Saas\Http\Controller\AppController;
use Saas\Http\Request\Auth as Auth;
use Saas\Http\Request\Collection as Collection;
use Saas\Http\Request\Admin as Admin;
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
    $auth->add('revoke-token', '/revoke-token')->methods(['POST'])->controller(Auth\RevokeTokenRequest::class);
    $auth->add('email', '/email')
        ->methods(['POST'])
        ->controller(Auth\EmailAuthRequest::class)
        ->options(['auth' => false]);
    
    // Admin
    $user = $routes->collection('saas.admin.')->prefix('/saas/admin');
    $user->add('profile', '/profile')->methods(['POST'])->controller(Admin\ProfileRequest::class);
    $user->add('avatar', '/avatar')->methods(['POST'])->controller(Admin\UploadAvatarRequest::class);
    
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
    $routes->add('saas.resources.show', '/saas/resources/show')
        ->methods(['POST'])
        ->controller(\Saas\Http\Request\Resource\ShowAllRequest::class);
};