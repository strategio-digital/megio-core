<?php

declare(strict_types=1);

namespace Megio\Tests\Hooray;

use App\Hooray\Database\Entity\Customer\ApprovalPage;
use App\Hooray\Database\Entity\Order\Order;
use App\Hooray\Database\Entity\Order\Status;
use App\Hooray\Recipe\ApprovalPageRecipe;
use Megio\Http\Request\Collection\CreateRequest;

test('hooray - create approval page', function () {
    $recipe = $this->createCollectionRecipe(ApprovalPageRecipe::class);
    $request = $this->createCollectionRequest(CreateRequest::class);
    
    $status = new Status();
    $status->setKey($this->generator()->slug());
    $status->setCuteName($this->generator()->name());
    $this->em()->persist($status);
    
    $order = new Order();
    $order->setEmail($this->generator()->email());
    $order->setOrderNumber((string)$this->generator()->numberBetween(5000, 10000));
    $order->setStatus($status);
    $this->em()->persist($order);
    
    $this->em()->flush();
    
    $data = [
        'recipe' => $recipe->key(),
        'rows' => [
            [
                'items' => json_encode(['a' => 'b']),
                'order_' => $order->getId()
            ]
        ]
    ];
    
    $response = $request->process($data);
    expect($response->getStatusCode())->toBe(200, (string)$response->getContent());
    
    $respData = json_decode((string)$response->getContent(), true);
    expect($respData)->toBeArray();
    
    $id = $respData['ids'][0];
    
    $row = $this->em()->getRepository(ApprovalPage::class)->findOneBy(['id' => $id]);
    expect($row)->toBeInstanceOf(ApprovalPage::class);
});
