<?php

declare(strict_types=1);

namespace Megio\Tests\Collection;

use Megio\Database\Entity\Admin;
use Megio\Http\Request\Collection\UpdateRequest;
use Megio\Recipe\AdminRecipe;

test('collection update row', function () {
    
    // Create db row
    $email = $this->generator()->email();
    $admin = new Admin();
    $admin->setEmail($email);
    $admin->setPassword($this->generator()->password());
    $admin->setLastLogin(new \DateTime());
    $this->em()->persist($admin);
    $this->em()->flush($admin);
    
    // Update email value in db row
    $newEmail = $this->generator()->email();
    $recipe = $this->createCollectionRecipe(AdminRecipe::class);
    $request = $this->createCollectionRequest(UpdateRequest::class);
    
    $data = [
        'recipe' => $recipe->key(),
        'rows' => [
            [
                'id' => $admin->getId(),
                'data' => [
                    'email' => $newEmail
                ]
            ]
        ]
    ];
    
    $response = $request->process($data);
    expect($response->getStatusCode())->toBe(200, (string)$response->getContent());
    
    $row = $this->em()->getAdminRepo()->findOneBy(['email' => $newEmail]);
    
    expect($row)
        ->toBeInstanceOf(Admin::class)
        ->and($row->getEmail())
        ->toBe($newEmail, 'Email should be updated');
    
});
