<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

use Saas\Http\Request\Crud\CreateRequest;
use Saas\Http\Request\Crud\DeleteRequest;
use Saas\Http\Request\Crud\ShowOneRequest;
use Saas\Http\Request\Crud\ShowRequest;
use Saas\Http\Request\Crud\UpdateRequest;
use Saas\Security\Guard\ResourceGuard;
use Saas\Security\Permissions\DefaultResource;

class CrudController extends Controller
{
    #[ResourceGuard([DefaultResource::CrudCrete])]
    public function create(CreateRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::CrudShowOne])]
    public function showOne(ShowOneRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::CrudShow])]
    public function show(ShowRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::CrudUpdate])]
    public function update(UpdateRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::CrudDelete])]
    public function delete(DeleteRequest $request): void
    {
    }
}