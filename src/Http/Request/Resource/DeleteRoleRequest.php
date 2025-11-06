<?php
declare(strict_types=1);

namespace Megio\Http\Request\Resource;

use Megio\Database\EntityManager;
use Megio\Http\Request\AbstractRequest;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteRoleRequest extends AbstractRequest
{
    public function __construct(protected EntityManager $em) {}

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        return [
            'id' => Expect::string()->required(),
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function processValidatedData(array $data): Response
    {
        $role = $this->em->getAuthRoleRepo()->findOneBy(['id' => $data['id']]);

        if (!$role) {
            return $this->error(['errors' => ['This role is already deleted']], 404);
        }

        $this->em->remove($role);
        $this->em->flush();

        return $this->json(['message' => 'Role successfully deleted']);
    }

    public function process(Request $request): Response
    {
        return new Response('ProcessValidatedData() is deprecated', 500);
    }
}
