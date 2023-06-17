<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection\Meta;

use Saas\Database\CrudHelper\CrudHelper;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;

class NavbarRequest implements IRequest
{
    public function __construct(
        protected readonly CrudHelper $helper,
        protected readonly Response   $response
    )
    {
    }
    
    public function schema(): array
    {
        return [];
    }
    
    public function process(array $data): void
    {
        $tables = array_map(fn($meta) => $meta['table'], $this->helper->getAllEntityClassNames());
        sort($tables);
        $this->response->send(['items' => $tables]);
    }
}