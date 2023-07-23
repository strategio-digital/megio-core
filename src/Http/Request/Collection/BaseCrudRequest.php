<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Collection;

use Saas\Database\CrudHelper\CrudHelper;
use Saas\Database\CrudHelper\EntityMetadata;
use Saas\Http\Request\Request;

abstract class BaseCrudRequest extends Request
{
    protected readonly CrudHelper $helper; // @phpstan-ignore-line (injected in child class)
    
    public function setUpMetadata(string $tableName, bool $schema, string $visiblePropsProperty = null,): ?EntityMetadata
    {
        $visiblePropsProperty = $visiblePropsProperty ?? CrudHelper::PROPERTY_SHOW_ALL;
        return $this->helper->getEntityMetadata($tableName, $visiblePropsProperty, $schema);
    }
}