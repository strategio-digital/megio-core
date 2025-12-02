<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Serializer\ValidatorTranslatorAdapter;

use Megio\Http\Serializer\ValidatorTranslatorAdapter;
use Megio\Translation\Translator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SimpleMessagesTest extends TestCase
{
    /**
     * @return array<string, array{symfonyMessage: string, expectedKey: string, translatedMessage: string}>
     */
    public static function simpleMessagesProvider(): array
    {
        return [
            'not_blank' => [
                'symfonyMessage' => 'This value should not be blank.',
                'expectedKey' => 'validator.not_blank',
                'translatedMessage' => 'Tato hodnota nesmí být prázdná.',
            ],
            'email' => [
                'symfonyMessage' => 'This value is not a valid email address.',
                'expectedKey' => 'validator.email',
                'translatedMessage' => 'Tato hodnota není platná e-mailová adresa.',
            ],
            'is_true' => [
                'symfonyMessage' => 'This value should be true.',
                'expectedKey' => 'validator.is_true',
                'translatedMessage' => 'Tato hodnota musí být pravdivá.',
            ],
            'is_false' => [
                'symfonyMessage' => 'This value should be false.',
                'expectedKey' => 'validator.is_false',
                'translatedMessage' => 'Tato hodnota musí být nepravdivá.',
            ],
            'url' => [
                'symfonyMessage' => 'This value is not a valid URL.',
                'expectedKey' => 'validator.url',
                'translatedMessage' => 'Tato hodnota není platná URL adresa.',
            ],
            'uuid' => [
                'symfonyMessage' => 'This is not a valid UUID.',
                'expectedKey' => 'validator.uuid',
                'translatedMessage' => 'Toto není platné UUID.',
            ],
            'json' => [
                'symfonyMessage' => 'This value should be valid JSON.',
                'expectedKey' => 'validator.json',
                'translatedMessage' => 'Tato hodnota musí být platný JSON.',
            ],
        ];
    }

    #[DataProvider('simpleMessagesProvider')]
    public function testTranslatesSimpleMessages(
        string $symfonyMessage,
        string $expectedKey,
        string $translatedMessage,
    ): void {
        $translator = $this->createMock(Translator::class);
        $translator
            ->expects($this->once())
            ->method('translate')
            ->with($expectedKey, [])
            ->willReturn($translatedMessage);

        $adapter = new ValidatorTranslatorAdapter($translator);
        $result = $adapter->trans($symfonyMessage);

        $this->assertSame($translatedMessage, $result);
    }
}
