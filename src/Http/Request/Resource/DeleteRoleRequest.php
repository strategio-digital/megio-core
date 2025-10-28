<?php
declare(strict_types=1);

namespace Megio\Http\Request\Resource;

use Megio\Database\EntityManager;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Response;

class DeleteRoleRequest extends Request
{
    public function __construct(protected EntityManager $em) {}

    public function schema(array $data): array
    {
        return [
            'id' => Expect::string()->required(),
        ];
    }

    public function process(array $data): Response
    {
        $role = $this->em->getAuthRoleRepo()->findOneBy(['id' => $data['id']]);

        if (!$role) {
            return $this->error(['This role is already deleted'], 404);
        }

        $this->em->remove($role);
        $this->em->flush();

        return $this->json(['message' => 'Role successfully deleted']);
    }
}
