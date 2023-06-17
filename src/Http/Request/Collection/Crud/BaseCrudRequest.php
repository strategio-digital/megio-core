<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection\Crud;

use Saas\Database\CrudHelper\CrudHelper;
use Saas\Database\CrudHelper\EntityMetadata;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;

abstract class BaseCrudRequest implements IRequest
{
    protected readonly CrudHelper $helper; // @phpstan-ignore-line (injected in child class)
    protected readonly Response $response; // @phpstan-ignore-line (injected in child class)
    
    public function setUpMetadata(string $tableName): EntityMetadata
    {
        $meta = $this->helper->getEntityMetadata($tableName);
        
        if (!$meta) {
            $this->response->sendError([$this->helper->getError()], 404);
        }
        
        return $meta;
    }
}