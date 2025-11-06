<?php
declare(strict_types=1);

namespace Megio\Http\Request\Resource;

use Doctrine\ORM\Exception\ORMException;
use Megio\Database\Enum\ResourceType;
use Symfony\Component\HttpFoundation\Response;

class UpdateResourceRequest extends ReadAllRequest
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws ORMException
     */
    public function processValidatedData(array $data): Response
    {
        $this->manager->updateResources(true, $data['view_resources'], ...ResourceType::cases());
        return parent::processValidatedData($data);
    }
}
