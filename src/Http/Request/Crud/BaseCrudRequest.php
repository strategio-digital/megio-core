<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Crud;

use Saas\Database\CrudHelper\CrudHelper;
use Saas\Database\CrudHelper\EntityMetadata;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;

abstract class BaseCrudRequest implements IRequest
{
    public function __construct(
        protected readonly CrudHelper $helper,
        protected readonly Response $response
    )
    {}
    
    public function setUpMetadata(string $tableName): EntityMetadata
    {
        $meta = $this->helper->getEntityMetadata($tableName);
        
        if (!$meta) {
            $this->response->sendError([$this->helper->getError()], 404);
        }
        
        return $meta;
    }
}