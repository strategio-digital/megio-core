<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection\Meta;

use Saas\Database\CrudHelper\CrudHelper;
use Saas\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class NavbarRequest extends Request
{
    public function __construct(protected readonly CrudHelper $helper)
    {
    }
    
    public function schema(): array
    {
        return [];
    }
    
    public function process(array $data): Response
    {
        $classes = $this->helper->getAllEntities();
        $classes = array_filter($classes, fn($class) => !in_array($class['value'], CrudHelper::INVISIBLE_IN_COLLECTION_NAV));
        $tables = array_map(fn($class) => $class['table'], $classes);
        
        sort($tables);
        
        return $this->json(['items' => $tables]);
    }
}