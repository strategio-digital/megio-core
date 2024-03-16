<?php
declare(strict_types=1);

namespace Megio\Http\Request\Resource;

use Megio\Database\Enum\ResourceType;
use Symfony\Component\HttpFoundation\Response;

class UpdateResourceRequest extends ShowAllRequest
{
    public function process(array $data): Response
    {
        $this->manager->updateResources(true, $data['view_resources'], ...ResourceType::cases());
        return parent::process($data);
    }
}