<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Resource;

use Saas\Database\Enum\ResourceType;
use Symfony\Component\HttpFoundation\Response;

class UpdateResourceRequest extends ShowAllRequest
{
    public function process(array $data): Response
    {
        $this->manager->updateResources(true, $data['view_resources'], ...ResourceType::cases());
        return parent::process($data);
    }
}