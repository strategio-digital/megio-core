<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Serializer\ValidatorTranslatorAdapter;

use Megio\Http\Serializer\ValidatorTranslatorAdapter;
use Megio\Translation\Translator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const JSON_THROW_ON_ERROR;

class ParameterizedMessagesTest extends TestCase
{
    /**
     * @return array<string, array{symfonyMessage: string, symfonyParams: array<string, mixed>, expectedKey: string, expectedIcuParams: array<string, mixed>, translatedMessage: string}>
     */
    public static function messagesWithParametersProvider(): array
    {
        return [
            'type_with_param' => [
                'symfonyMessage' => 'This value should be of type {{ type }}.',
                'symfonyParams' => ['{{ type }}' => 'string'],
                'expectedKey' => 'validator.type',
                'expectedIcuParams' => ['type' => 'string'],
                'translatedMessage' => 'Tato hodnota musí být typu string.',
            ],
            'range_max' => [
                'symfonyMessage' => 'This value should be {{ limit }} or less.',
                'symfonyParams' => ['{{ limit }}' => 100],
                'expectedKey' => 'validator.range_max',
                'expectedIcuParams' => ['limit' => 100],
                'translatedMessage' => 'Tato hodnota musí být 100 nebo méně.',
            ],
            'range_min' => [
                'symfonyMessage' => 'This value should be {{ limit }} or more.',
                'symfonyParams' => ['{{ limit }}' => 5],
                'expectedKey' => 'validator.range_min',
                'expectedIcuParams' => ['limit' => 5],
                'translatedMessage' => 'Tato hodnota musí být 5 nebo více.',
            ],
            'range_between' => [
                'symfonyMessage' => 'This value should be between {{ min }} and {{ max }}.',
                'symfonyParams' => ['{{ min }}' => 1, '{{ max }}' => 100],
                'expectedKey' => 'validator.range',
                'expectedIcuParams' => ['min' => 1, 'max' => 100],
                'translatedMessage' => 'Tato hodnota musí být mezi 1 a 100.',
            ],
            'equal_to' => [
                'symfonyMessage' => 'This value should be equal to {{ compared_value }}.',
                'symfonyParams' => ['{{ compared_value }}' => 42],
                'expectedKey' => 'validator.equal_to',
                'expectedIcuParams' => ['comparedValue' => 42],
                'translatedMessage' => 'Tato hodnota musí být rovna 42.',
            ],
            'greater_than' => [
                'symfonyMessage' => 'This value should be greater than {{ compared_value }}.',
                'symfonyParams' => ['{{ compared_value }}' => 10],
                'expectedKey' => 'validator.greater_than',
                'expectedIcuParams' => ['comparedValue' => 10],
                'translatedMessage' => 'Tato hodnota musí být větší než 10.',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $symfonyParams
     * @param array<string, mixed> $expectedIcuParams
     */
    #[DataProvider('messagesWithParametersProvider')]
    public function testTranslatesMessagesWithParameters(
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

    /**
     * @return array<string, array{input: string, expected: string}>
     */
    public static function snakeToCamelProvider(): array
    {
        return [
            'simple' => ['input' => 'compared_value', 'expected' => 'comparedValue'],
            'multiple_underscores' => ['input' => 'max_file_size', 'expected' => 'maxFileSize'],
            'no_underscore' => ['input' => 'limit', 'expected' => 'limit'],
            'single_letter_parts' => ['input' => 'a_b_c', 'expected' => 'aBC'],
        ];
    }

    #[DataProvider('snakeToCamelProvider')]
    public function testSnakeToCamelConversion(string $input, string $expected): void
    {
        $translator = $this->createMock(Translator::class);
        $translator
            ->method('translate')
            ->willReturnCallback(function (string $key, array $params) {
                return json_encode($params, JSON_THROW_ON_ERROR);
            });

        $adapter = new ValidatorTranslatorAdapter($translator);

        $symfonyKey = '{{ ' . $input . ' }}';
        $result = $adapter->trans('This value should be of type {{ type }}.', [$symfonyKey => 'test']);

        $decoded = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey($expected, $decoded);
    }
}
