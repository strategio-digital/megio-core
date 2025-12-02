<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Serializer\ValidatorTranslatorAdapter;

use Megio\Http\Serializer\ValidatorTranslatorAdapter;
use Megio\Translation\Translator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PluralMessagesTest extends TestCase
{
    /**
     * @return array<string, array{symfonyMessage: string, symfonyParams: array<string, mixed>, expectedKey: string, expectedIcuParams: array<string, mixed>, translatedMessage: string}>
     */
    public static function pluralMessagesProvider(): array
    {
        return [
            'length_max_singular' => [
                'symfonyMessage' => 'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.',
                'symfonyParams' => ['{{ limit }}' => 1, '%count%' => 1],
                'expectedKey' => 'validator.length_max',
                'expectedIcuParams' => ['limit' => 1, 'count' => 1],
                'translatedMessage' => 'Tato hodnota je příliš dlouhá. Měla by mít maximálně 1 znak.',
            ],
            'length_max_few' => [
                'symfonyMessage' => 'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.',
                'symfonyParams' => ['{{ limit }}' => 3, '%count%' => 3],
                'expectedKey' => 'validator.length_max',
                'expectedIcuParams' => ['limit' => 3, 'count' => 3],
                'translatedMessage' => 'Tato hodnota je příliš dlouhá. Měla by mít maximálně 3 znaky.',
            ],
            'length_max_many' => [
                'symfonyMessage' => 'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.',
                'symfonyParams' => ['{{ limit }}' => 10, '%count%' => 10],
                'expectedKey' => 'validator.length_max',
                'expectedIcuParams' => ['limit' => 10, 'count' => 10],
                'translatedMessage' => 'Tato hodnota je příliš dlouhá. Měla by mít maximálně 10 znaků.',
            ],
            'length_min_singular' => [
                'symfonyMessage' => 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
                'symfonyParams' => ['{{ limit }}' => 1, '%count%' => 1],
                'expectedKey' => 'validator.length_min',
                'expectedIcuParams' => ['limit' => 1, 'count' => 1],
                'translatedMessage' => 'Tato hodnota je příliš krátká. Měla by mít minimálně 1 znak.',
            ],
            'length_min_few' => [
                'symfonyMessage' => 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
                'symfonyParams' => ['{{ limit }}' => 2, '%count%' => 2],
                'expectedKey' => 'validator.length_min',
                'expectedIcuParams' => ['limit' => 2, 'count' => 2],
                'translatedMessage' => 'Tato hodnota je příliš krátká. Měla by mít minimálně 2 znaky.',
            ],
            'length_min_many' => [
                'symfonyMessage' => 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
                'symfonyParams' => ['{{ limit }}' => 100, '%count%' => 100],
                'expectedKey' => 'validator.length_min',
                'expectedIcuParams' => ['limit' => 100, 'count' => 100],
                'translatedMessage' => 'Tato hodnota je příliš krátká. Měla by mít minimálně 100 znaků.',
            ],
            'length_exact_singular' => [
                'symfonyMessage' => 'This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.',
                'symfonyParams' => ['{{ limit }}' => 1, '%count%' => 1],
                'expectedKey' => 'validator.length_exact',
                'expectedIcuParams' => ['limit' => 1, 'count' => 1],
                'translatedMessage' => 'Tato hodnota musí mít přesně 1 znak.',
            ],
            'length_exact_few' => [
                'symfonyMessage' => 'This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.',
                'symfonyParams' => ['{{ limit }}' => 4, '%count%' => 4],
                'expectedKey' => 'validator.length_exact',
                'expectedIcuParams' => ['limit' => 4, 'count' => 4],
                'translatedMessage' => 'Tata hodnota musí mít přesně 4 znaky.',
            ],
            'choice_min_singular' => [
                'symfonyMessage' => 'You must select at least {{ limit }} choice.|You must select at least {{ limit }} choices.',
                'symfonyParams' => ['{{ limit }}' => 1, '%count%' => 1],
                'expectedKey' => 'validator.choice_min',
                'expectedIcuParams' => ['limit' => 1, 'count' => 1],
                'translatedMessage' => 'Musíte vybrat alespoň 1 možnost.',
            ],
            'choice_min_few' => [
                'symfonyMessage' => 'You must select at least {{ limit }} choice.|You must select at least {{ limit }} choices.',
                'symfonyParams' => ['{{ limit }}' => 3, '%count%' => 3],
                'expectedKey' => 'validator.choice_min',
                'expectedIcuParams' => ['limit' => 3, 'count' => 3],
                'translatedMessage' => 'Musíte vybrat alespoň 3 možnosti.',
            ],
            'choice_max_singular' => [
                'symfonyMessage' => 'You must select at most {{ limit }} choice.|You must select at most {{ limit }} choices.',
                'symfonyParams' => ['{{ limit }}' => 1, '%count%' => 1],
                'expectedKey' => 'validator.choice_max',
                'expectedIcuParams' => ['limit' => 1, 'count' => 1],
                'translatedMessage' => 'Můžete vybrat maximálně 1 možnost.',
            ],
            'choice_max_many' => [
                'symfonyMessage' => 'You must select at most {{ limit }} choice.|You must select at most {{ limit }} choices.',
                'symfonyParams' => ['{{ limit }}' => 5, '%count%' => 5],
                'expectedKey' => 'validator.choice_max',
                'expectedIcuParams' => ['limit' => 5, 'count' => 5],
                'translatedMessage' => 'Můžete vybrat maximálně 5 možností.',
            ],
            'count_min_singular' => [
                'symfonyMessage' => 'This collection should contain {{ limit }} element or more.|This collection should contain {{ limit }} elements or more.',
                'symfonyParams' => ['{{ limit }}' => 1, '%count%' => 1],
                'expectedKey' => 'validator.count_min',
                'expectedIcuParams' => ['limit' => 1, 'count' => 1],
                'translatedMessage' => 'Tato kolekce musí obsahovat alespoň 1 prvek.',
            ],
            'count_min_few' => [
                'symfonyMessage' => 'This collection should contain {{ limit }} element or more.|This collection should contain {{ limit }} elements or more.',
                'symfonyParams' => ['{{ limit }}' => 2, '%count%' => 2],
                'expectedKey' => 'validator.count_min',
                'expectedIcuParams' => ['limit' => 2, 'count' => 2],
                'translatedMessage' => 'Tato kolekce musí obsahovat alespoň 2 prvky.',
            ],
            'count_max_many' => [
                'symfonyMessage' => 'This collection should contain {{ limit }} element or less.|This collection should contain {{ limit }} elements or less.',
                'symfonyParams' => ['{{ limit }}' => 10, '%count%' => 10],
                'expectedKey' => 'validator.count_max',
                'expectedIcuParams' => ['limit' => 10, 'count' => 10],
                'translatedMessage' => 'Tato kolekce může obsahovat maximálně 10 prvků.',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $symfonyParams
     * @param array<string, mixed> $expectedIcuParams
     */
    #[DataProvider('pluralMessagesProvider')]
    public function testTranslatesPluralMessages(
        string $symfonyMessage,
        array $symfonyParams,
        string $expectedKey,
        array $expectedIcuParams,
        string $translatedMessage,
    ): void {
        $translator = $this->createMock(Translator::class);
        $translator
            ->expects($this->once())
            ->method('translate')
            ->with($expectedKey, $expectedIcuParams)
            ->willReturn($translatedMessage);

        $adapter = new ValidatorTranslatorAdapter($translator);
        $result = $adapter->trans($symfonyMessage, $symfonyParams);

        $this->assertSame($translatedMessage, $result);
    }
}
