<?php
declare(strict_types=1);

namespace Megio\Http\Request\Admin;

use Megio\Database\EntityManager;
use Megio\Http\Request\AbstractRequest;
use Megio\Security\Auth\AuthUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileRequest extends AbstractRequest
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly AuthUser $user,
    ) {}

    public function process(Request $request): Response
    {
        $user = $this->user->get();

        if (!$user) {
            return $this->error(['You are not logged in']);
        }

        $roles = $user->getRoles()->map(fn(
            $role,
        ) => $role->getName())->toArray();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $roles,
            'last_login' => $user->getLastLogin(),
        ]);
    }
}
