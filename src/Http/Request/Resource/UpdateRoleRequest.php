<?php
declare(strict_types=1);

namespace Megio\Http\Request\Resource;

use Megio\Database\EntityManager;
use Megio\Http\Request\AbstractRequest;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateRoleRequest extends AbstractRequest
{
    public function __construct(protected EntityManager $em) {}

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        return [
            'resource_id' => Expect::string()->required(),
            'role_id' => Expect::string()->required(),
            'enable' => Expect::bool()->required(),
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function processValidatedData(array $data): Response
    {
        $resource = $this->em->getAuthResourceRepo()->findOneBy(['id' => $data['resource_id']]);
        $role = $this->em->getAuthRoleRepo()->findOneBy(['id' => $data['role_id']]);

        if (!$role || !$resource) {
            return $this->error(['Role or resource not found'], 404);
        }

        if ($data['enable'] === true) {
            $role->addResource($resource);
        } else {
            $role->getResources()->removeElement($resource);
        }

        $this->em->flush();

        return $this->json(['message' => 'Resources successfully updated']);
    }

    public function process(Request $request): Response
    {
        return new Response('ProcessValidatedData() is deprecated', 500);
    }
}
