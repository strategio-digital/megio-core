<?php

declare(strict_types=1);

namespace Tests\Feature\Doctrine;

use DateTime;
use Faker\Factory;
use Megio\Database\Entity\Admin;
use Symfony\Component\Uid\UuidV6;
use Tests\TestCase;

class CreateEntityTest extends TestCase
{
    public function testDoctrineSimpleEntityFromArray(): void
    {
        $faker = Factory::create();
        $email = $faker->email();

        $admin = clone $this->em->getUnitOfWork()->createEntity(Admin::class, [
            'id' => UuidV6::generate(),
            'email' => $email,
            'password' => $faker->password(),
            'lastLogin' => new DateTime(),
        ]);

        $this->assertInstanceOf(Admin::class, $admin);

        $this->em->persist($admin);
        $this->em->flush();

        $row = $this->em->getAdminRepo()->findOneBy(['email' => $email]);
        $this->assertInstanceOf(Admin::class, $row);
        $this->assertEquals($email, $row->getEmail());
    }
}
