<?php declare(strict_types=1);

use Megio\Helper\Router;
use Megio\Http\Controller\AppController;
use Megio\Http\Request\Admin as Admin;
use Megio\Http\Request\Auth as Auth;
use Megio\Http\Request\Collection as Collection;
use Megio\Http\Request\Resource as Resource;
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
        ->controller([AppController::class, 'api']);

    // Auth
    $auth = $routes->collection('megio.auth.')->prefix('/megio/auth');

    $auth->add('email', '/email')
        ->methods(['POST'])
        ->controller(Auth\EmailAuthRequest::class)
        ->options(['auth' => false]);

    $auth->add('revoke-token', '/revoke-token')
        ->methods(['POST'])
        ->controller(Auth\RevokeTokenRequest::class)
        ->options(['inResources' => false]);

    // Admin
    $admin = $routes->collection('megio.admin.')->prefix('/megio/admin');

    $admin->add('profile', '/profile')
        ->methods(['POST'])
        ->controller(Admin\ProfileRequest::class)
        ->options(['inResources' => false]);

    $admin->add('avatar', '/avatar')
        ->methods(['POST'])
        ->controller(Admin\UploadAvatarRequest::class)
        ->options(['inResources' => false]);

    // Collections navbar
    $routes->add(Router::ROUTE_META_NAVBAR, '/megio/collections/navbar')
        ->methods(['POST'])
        ->controller(Collection\NavbarRequest::class);

    // Collection Forms
    $form = $routes->collection('megio.collection.form.')->prefix('/megio/collections/form');
    $form->add('creating', '/creating')
        ->methods(['POST'])
        ->controller(Collection\Form\CreatingFormRequest::class);

    $form->add('updating', '/updating')
        ->methods(['POST'])
        ->controller(Collection\Form\UpdatingFormRequest::class);

    // Collections
    $collection = $routes->collection(Router::ROUTE_COLLECTION_PREFIX)->prefix('/megio/collections');
    $collection->add('read', '/read')->methods(['POST'])->controller(Collection\ReadRequest::class);
    $collection->add('read-all', '/read-all')->methods(['POST'])->controller(Collection\ReadAllRequest::class);
    $collection->add('create', '/create')->methods(['POST'])->controller(Collection\CreateRequest::class);
    $collection->add('delete', '/delete')->methods(['DELETE'])->controller(Collection\DeleteRequest::class);
    $collection->add('update', '/update')->methods(['PATCH'])->controller(Collection\UpdateRequest::class);

    // Resources
    $resources = $routes->collection('megio.resources.')->prefix('/megio/resources');

    $resources->add('read-all', '/read-all')
        ->methods(['POST'])
        ->controller(Resource\ReadAllRequest::class)
        ->options(['inResources' => false]);

    $resources->add('update', '/update')
        ->methods(['POST'])
        ->controller(Resource\UpdateResourceRequest::class)
        ->options(['inResources' => false]);

    $resources->add('update.role', '/update-role')
        ->methods(['POST'])
        ->controller(Resource\UpdateRoleRequest::class)
        ->options(['inResources' => false]);

    $resources->add('create.role', '/create-role')
        ->methods(['POST'])
        ->controller(Resource\CreateRoleRequest::class)
        ->options(['inResources' => false]);

    $resources->add('delete.role', '/delete-role')
        ->methods(['DELETE'])
        ->controller(Resource\DeleteRoleRequest::class)
        ->options(['inResources' => false]);
};
