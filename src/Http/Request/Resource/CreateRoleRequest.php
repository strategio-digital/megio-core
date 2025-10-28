<?php
declare(strict_types=1);

namespace Megio\Http\Request\Resource;

use Megio\Database\Entity\Auth\Role;
use Megio\Database\EntityManager;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Nette\Utils\Strings;
use Symfony\Component\HttpFoundation\Response;

class CreateRoleRequest extends Request
{
    public function __construct(protected EntityManager $em) {}

    public function schema(array $data): array
    {
        return [
            'name' => Expect::string()->min(3)->max(32)->required(),
        ];
    }

    public function process(array $data): Response
    {
        $name = Strings::webalize($data['name']);
        $role = $this->em->getAuthRoleRepo()->findOneBy(['name' => $name]);

        if ($role) {
            return $this->error(['This role already exists']);
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
}
