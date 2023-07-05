<?php

use Saas\Helper\Router;
use Saas\Http\Controller\AppController;
use Saas\Http\Request\Auth as Auth;
use Saas\Http\Request\Collection\Crud as Crud;
use Saas\Http\Request\Collection\Meta\NavbarRequest;
use Saas\Http\Request\User as User;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    // Admin
    $routes->add(Router::ROUTE_ADMIN, '/admin{uri}')
        ->methods(['GET'])
        ->controller([AppController::class, 'app'])
        ->requirements(['uri' => '.*']);
    
    // Api
    $api = $routes->collection()->prefix('/api');
    $api->add(Router::ROUTE_API, '/')->methods(['GET'])->controller([AppController::class, 'api']);
    
    // Auth
    $auth = $api->collection('auth.')->prefix('/auth');
    $auth->add('email', '/email')->methods(['POST'])->controller(Auth\EmailAuthRequest::class);
    $auth->add('revoke-token', '/revoke-token')->methods(['POST'])->controller(Auth\RevokeTokenRequest::class);
    
    // User extra
    $user = $api->collection('user.')->prefix('/user');
    $user->add('profile', '/show-profile')->methods(['POST'])->controller(User\ProfileRequest::class);
    $user->add('avatar', '/upload-avatar')->methods(['POST'])->controller(User\UploadAvatarRequest::class);
    
    // Saas collections CRUD
    $crud = $api->collection('saas.crud.')->prefix('/saas/crud');
    $crud->add('show', '/show')->methods(['POST'])->controller(Crud\ShowRequest::class);
    $crud->add('show-one', '/show-one')->methods(['POST'])->controller(Crud\ShowOneRequest::class);
    $crud->add('create', '/create')->methods(['POST'])->controller(Crud\CreateRequest::class);
    $crud->add('delete', '/delete')->methods(['DELETE'])->controller(Crud\DeleteRequest::class);
    $crud->add('update', '/update')->methods(['PATCH'])->controller(Crud\UpdateRequest::class);
    
    // Saas collections meta
    $meta = $api->collection('saas.meta.')->prefix('/saas/meta');
    $meta->add('navbar', '/navbar')->methods(['POST'])->controller(NavbarRequest::class);
};