<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Controller;

use Saas\Http\Request\Collection\Crud\CreateRequest;
use Saas\Http\Request\Collection\Crud\DeleteRequest;
use Saas\Http\Request\Collection\Crud\ShowOneRequest;
use Saas\Http\Request\Collection\Crud\ShowRequest;
use Saas\Http\Request\Collection\Crud\UpdateRequest;
use Saas\Security\Guard\ResourceGuard;
use Saas\Security\Permissions\DefaultResource;

class SaasCrudController extends Controller
{
    #[ResourceGuard([DefaultResource::SaasCrudCrete])]
    public function create(CreateRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::SaasCrudShowOne])]
    public function showOne(ShowOneRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::SaasCrudShow])]
    public function show(ShowRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::SaasCrudUpdate])]
    public function update(UpdateRequest $request): void
    {
    }
    
    #[ResourceGuard([DefaultResource::SaasCrudDelete])]
    public function delete(DeleteRequest $request): void
    {
    }
}