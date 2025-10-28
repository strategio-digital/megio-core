<?php

declare(strict_types=1);

namespace Tests\Feature\Collection;

use Doctrine\ORM\Exception\ORMException;
use Megio\Database\Entity\Admin;
use Megio\Http\Request\Collection\CreateRequest;
use Megio\Recipe\AdminRecipe;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @phpstan-type RowData array<string, string|int|float|bool|null>
 * @phpstan-type FieldValue string|int|float|bool|null
 */
class CreateRowsTest extends TestCase
{
    /**
     * @param RowData $rowData
     *
     * @throws ORMException
     */
    #[DataProvider('createDataProvider')]
    public function testCollectionCreateRow(
        array $rowData,
        string $fieldToCheck,
        string $expectedValue,
    ): void {
        $recipe = $this->createCollectionRecipe(AdminRecipe::class);
        $request = $this->createCollectionRequest(CreateRequest::class);

        $data = [
            'recipeKey' => $recipe->key(),
            'rows' => [$rowData],
            'custom_data' => null,
        ];

        $response = $request->process($data);
        $this->assertEquals(200, $response->getStatusCode(), (string)$response->getContent());

        $row = $this->em->getAdminRepo()->findOneBy([$fieldToCheck => $expectedValue]);
        $this->assertInstanceOf(Admin::class, $row);

        $getter = 'get' . ucfirst($fieldToCheck);
        $this->assertEquals($expectedValue, $row->$getter());
    }

    /**
     * @return array<string, array{rowData: RowData, fieldToCheck: string, expectedValue: string}>
     */
    public static function createDataProvider(): array
    {
        $email = 'test-' . uniqid() . '@example.com';

        return [
            'create admin with email' => [
                'rowData' => [
                    'email' => $email,
                    'password' => 'TestPassword123',
                ],
                'fieldToCheck' => 'email',
                'expectedValue' => $email,
            ],
        ];
    }
}
