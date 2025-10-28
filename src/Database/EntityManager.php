<?php
declare(strict_types=1);

namespace Megio\Database;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Megio\Database\Entity\Admin;
use Megio\Database\Entity\Auth\Resource;
use Megio\Database\Entity\Auth\Role;
use Megio\Database\Entity\Auth\Token;
use Megio\Database\Entity\Queue;
use Megio\Database\Repository\AdminRepository;
use Megio\Database\Repository\Auth\ResourceRepository;
use Megio\Database\Repository\Auth\RoleRepository;
use Megio\Database\Repository\Auth\TokenRepository;
use Megio\Database\Repository\QueueRepository;
use Megio\Extension\Doctrine\Doctrine;

// @phpstan-ignore-next-line
class EntityManager extends \Doctrine\ORM\EntityManager implements EntityManagerInterface
{
    /**
     * @throws MissingMappingDriverImplementation
     */
    public function __construct(Doctrine $doctrine)
    {
        $em = $doctrine->entityManager;
        parent::__construct($em->getConnection(), $em->getConfiguration());
    }

    public function getAdminRepo(): AdminRepository
    {
        $repository = $this->getRepository(Admin::class);
        assert($repository instanceof AdminRepository);

        return $repository;
    }

    public function getAuthTokenRepo(): TokenRepository
    {
        $repository = $this->getRepository(Token::class);
        assert($repository instanceof TokenRepository);

        return $repository;
    }

    public function getAuthRoleRepo(): RoleRepository
    {
        $repository = $this->getRepository(Role::class);
        assert($repository instanceof RoleRepository);

        return $repository;
    }

    public function getAuthResourceRepo(): ResourceRepository
    {
        $repository = $this->getRepository(Resource::class);
        assert($repository instanceof ResourceRepository);

        return $repository;
    }

    public function getQueueRepo(): QueueRepository
    {
        $repository = $this->getRepository(Queue::class);
        assert($repository instanceof QueueRepository);

        return $repository;
    }
}
