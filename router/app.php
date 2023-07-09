<?php

use Saas\Helper\Router;
use Saas\Http\Controller\AppController;
use Saas\Http\Request\Auth as Auth;
use Saas\Http\Request\Collection\Crud as Crud;
use Saas\Http\Request\Collection\Meta as Meta;
use Saas\Http\Request\User as User;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    // Admin app
    $routes->add(Router::ROUTE_APP, '/admin{uri}')
        ->methods(['GET'])
        ->controller([AppController::class, 'app'])
        ->requirements(['uri' => '.*']);
    
    // Api overview
    $routes->add(Router::ROUTE_API, '/api')->methods(['GET'])->controller([AppController::class, 'api']);
    
    // Auth
    $auth = $routes->collection('saas.auth.')->prefix('/saas/auth');
    $auth->add('email', '/email')->methods(['POST'])->controller(Auth\EmailAuthRequest::class);
    $auth->add('revoke-token', '/revoke-token')->methods(['POST'])->controller(Auth\RevokeTokenRequest::class);
    
    // User extra
    $user = $routes->collection('saas.admin.')->prefix('/saas/admin');
    $user->add('profile', '/profile')->methods(['POST'])->controller(User\ProfileRequest::class);
    $user->add('avatar', '/avatar')->methods(['POST'])->controller(User\UploadAvatarRequest::class);
    
    // Saas collections
    $collection = $routes->collection(Router::ROUTE_COLLECTION_PREFIX)->prefix('/saas/collections');
    $collection->add('show', '/show')->methods(['POST'])->controller(Crud\ShowRequest::class);
    $collection->add('show-one', '/show-one')->methods(['POST'])->controller(Crud\ShowOneRequest::class);
    $collection->add('create', '/create')->methods(['POST'])->controller(Crud\CreateRequest::class);
    $collection->add('delete', '/delete')->methods(['DELETE'])->controller(Crud\DeleteRequest::class);
    $collection->add('update', '/update')->methods(['PATCH'])->controller(Crud\UpdateRequest::class);
    
    // Saas metadata
    $meta = $routes->collection(Router::ROUTE_META_PREFIX)->prefix('/saas/metadata');
    $meta->add('navbar', '/navbar')->methods(['POST'])->controller(Meta\NavbarRequest::class);
};