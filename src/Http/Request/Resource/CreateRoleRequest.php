<?php
declare(strict_types=1);

namespace Megio\Http\Request\Resource;

use Megio\Database\Entity\Auth\Role;
use Megio\Database\EntityManager;
use Megio\Http\Request\AbstractRequest;
use Nette\Schema\Expect;
use Nette\Utils\Strings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateRoleRequest extends AbstractRequest
{
    public function __construct(protected EntityManager $em) {}

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        return [
            'name' => Expect::string()->min(3)->max(32)->required(),
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function processValidatedData(array $data): Response
    {
        $name = Strings::webalize($data['name']);
        $role = $this->em->getAuthRoleRepo()->findOneBy(['name' => $name]);

        if ($role) {
            return $this->error(['errors' => ['This role already exists']]);
        }

        $role = new Role();
        $role->setName($name);

        $this->em->persist($role);
        $this->em->flush();

        return $this->json([
            'id' => $role->getId(),
            'name' => $role->getName(),
            'enabled' => false,
        ]);
    }

    public function process(Request $request): Response
    {
        return new Response('ProcessValidatedData() is deprecated', 500);
    }
}
