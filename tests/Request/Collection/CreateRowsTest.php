<?php

declare(strict_types=1);

namespace Megio\Tests\Request\Collection;

use Megio\Database\Entity\Admin;
use Megio\Http\Request\Collection\CreateRequest;
use Megio\Recipe\AdminRecipe;

test('request collection create rows', function () {
    $recipe = $this->createCollectionRecipe(AdminRecipe::class);
    $request = $this->createRequest(CreateRequest::class);
    
    $data = [
        'recipe' => $recipe->key(),
        'rows' => [
            [
                'email' => $this->generator()->email(),
                'password' => $this->generator()->password()
            ]
        ],
        'custom_data' => null
    ];
    
    $response = $request->process($data);
    expect($response->getStatusCode())->toBe(200, (string)$response->getContent());
    
    $row = $this->em()->getAdminRepo()->findOneBy(['email' => $data['rows'][0]['email']]);
    expect($row)->toBeInstanceOf(Admin::class);
})->repeat(5);
