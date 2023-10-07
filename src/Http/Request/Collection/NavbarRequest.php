<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Megio\Database\CrudHelper\CrudHelper;
use Megio\Database\Entity\Admin;
use Megio\Helper\Router;
use Megio\Http\Request\Request;
use Megio\Security\Auth\AuthUser;
use Symfony\Component\HttpFoundation\Response;

class NavbarRequest extends Request
{
    public function __construct(
        protected readonly AuthUser   $authUser,
        protected readonly CrudHelper $helper
    )
    {
    }
    
    public function schema(): array
    {
        return [];
    }
    
    public function process(array $data): Response
    {
        $classes = $this->helper->getAllEntities();
        $classes = array_filter($classes, fn($class) => $class['value'] !== Admin::class);
        $tables = array_map(fn($class) => $class['table'], $classes);
        
        if (!$this->authUser->get() instanceof Admin) {
            $resources = $this->authUser->getResources();
            $tables = array_filter($tables, fn($table) => in_array(Router::ROUTE_META_NAVBAR . '.' . $table, $resources));
        }
        
        sort($tables);
        
        return $this->json(['items' => $tables]);
    }
}