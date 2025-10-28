<?php

declare(strict_types=1);

namespace Tests\Feature\Collection;

use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Faker\Factory;
use Megio\Database\Entity\Admin;
use Megio\Database\Entity\EntityException;
use Megio\Http\Request\Collection\UpdateRequest;
use Megio\Recipe\AdminRecipe;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @phpstan-type FieldValue string|int|float|bool|null
 */
class UpdateRowsTest extends TestCase
{
    /**
     * @throws EntityException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[DataProvider('updateDataProvider')]
    public function testCollectionUpdateRow(
        string $field,
        string $newValue,
    ): void {
        $faker = Factory::create();

        // Create db row
        $admin = new Admin();
        $admin->setEmail($faker->email());
        $admin->setPassword($faker->password());
        $admin->setLastLogin(new DateTime());
        $this->em->persist($admin);
        $this->em->flush();

        // Update field value in db row
        $recipe = $this->createCollectionRecipe(AdminRecipe::class);
        $request = $this->createCollectionRequest(UpdateRequest::class);

        $data = [
            'recipeKey' => $recipe->key(),
            'rows' => [
                [
                    'id' => $admin->getId(),
                    'data' => [
                        $field => $newValue,
                    ],
                ],
            ],
        ];

        $response = $request->process($data);
        $this->assertEquals(200, $response->getStatusCode(), (string)$response->getContent());

        $row = $this->em->getAdminRepo()->findOneBy(['id' => $admin->getId()]);
        $this->assertInstanceOf(Admin::class, $row);

        $getter = 'get' . ucfirst($field);
        $this->assertEquals($newValue, $row->$getter(), "Field '{$field}' should be updated");
    }

    /**
     * @return array<string, array{field: string, newValue: string}>
     */
    public static function updateDataProvider(): array
    {
        return [
            'update email' => [
                'field' => 'email',
                'newValue' => 'newemail@example.com',
            ],
        ];
    }
}
