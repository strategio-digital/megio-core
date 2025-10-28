<?php
declare(strict_types=1);

namespace Tests\Doctrine;

use DateTime;
use Megio\Database\Entity\Admin;
use Symfony\Component\Uid\UuidV6;

test('doctrine simple entity from array', function (): void {
    $email = $this->generator()->email();

    $admin = clone $this->em()->getUnitOfWork()->createEntity(Admin::class, [
        'id' => UuidV6::generate(),
        'email' => $email,
        'password' => $this->generator()->password(),
        'lastLogin' => new DateTime(),
    ]);

    expect($admin)->toBeInstanceOf(Admin::class);

    $this->em()->persist($admin);
    $this->em()->flush($admin);

    $row = $this->em()->getAdminRepo()->findOneBy(['email' => $email]);
    expect($row)->toBeInstanceOf(Admin::class);
});
