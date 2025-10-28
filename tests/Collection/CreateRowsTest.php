<?php

declare(strict_types=1);

namespace Megio\Tests\Collection;

use Megio\Database\Entity\Admin;
use Megio\Http\Request\Collection\CreateRequest;
use Megio\Recipe\AdminRecipe;

test('collection create row', function (): void {
    $recipe = $this->createCollectionRecipe(AdminRecipe::class);
    $request = $this->createCollectionRequest(CreateRequest::class);

    $email = $this->generator()->email();

    $data = [
        'recipeKey' => $recipe->key(),
        'rows' => [
            [
                'email' => $email,
                'password' => $this->generator()->password(),
            ],
        ],
        'custom_data' => null,
    ];

    $response = $request->process($data);
    expect($response->getStatusCode())->toBe(200, (string)$response->getContent());

    $row = $this->em()->getAdminRepo()->findOneBy(['email' => $email]);
    expect($row)->toBeInstanceOf(Admin::class);
});
