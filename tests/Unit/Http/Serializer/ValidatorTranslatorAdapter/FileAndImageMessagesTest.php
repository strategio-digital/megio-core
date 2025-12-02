<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Serializer\ValidatorTranslatorAdapter;

use Megio\Http\Serializer\ValidatorTranslatorAdapter;
use Megio\Translation\Translator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileAndImageMessagesTest extends TestCase
{
    /**
     * @return array<string, array{symfonyMessage: string, symfonyParams: array<string, mixed>, expectedKey: string, expectedIcuParams: array<string, mixed>, translatedMessage: string}>
     */
    public static function fileMessagesProvider(): array
    {
        return [
            'file_too_large' => [
                'symfonyMessage' => 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.',
                'symfonyParams' => ['{{ size }}' => 5, '{{ suffix }}' => 'MB', '{{ limit }}' => 2],
                'expectedKey' => 'validator.file_too_large',
                'expectedIcuParams' => ['size' => 5, 'suffix' => 'MB', 'limit' => 2],
                'translatedMessage' => 'Soubor je příliš velký (5 MB). Maximální povolená velikost je 2 MB.',
            ],
            'file_invalid_mime' => [
                'symfonyMessage' => 'The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.',
                'symfonyParams' => ['{{ type }}' => 'application/pdf', '{{ types }}' => 'image/jpeg, image/png'],
                'expectedKey' => 'validator.file_invalid_mime',
                'expectedIcuParams' => ['type' => 'application/pdf', 'types' => 'image/jpeg, image/png'],
                'translatedMessage' => 'MIME typ souboru je neplatný (application/pdf). Povolené typy jsou image/jpeg, image/png.',
            ],
            'file_empty' => [
                'symfonyMessage' => 'An empty file is not allowed.',
                'symfonyParams' => [],
                'expectedKey' => 'validator.file_empty',
                'expectedIcuParams' => [],
                'translatedMessage' => 'Prázdný soubor není povolen.',
            ],
        ];
    }

    /**
     * @return array<string, array{symfonyMessage: string, symfonyParams: array<string, mixed>, expectedKey: string, expectedIcuParams: array<string, mixed>, translatedMessage: string}>
     */
    public static function imageMessagesProvider(): array
    {
        return [
            'image_width_too_big' => [
                'symfonyMessage' => 'The image width is too big ({{ width }}px). Allowed maximum width is {{ max_width }}px.',
                'symfonyParams' => ['{{ width }}' => 2000, '{{ max_width }}' => 1920],
                'expectedKey' => 'validator.image_width_too_big',
                'expectedIcuParams' => ['width' => 2000, 'maxWidth' => 1920],
                'translatedMessage' => 'Šířka obrázku je příliš velká (2000px). Maximální povolená šířka je 1920px.',
            ],
            'image_height_too_small' => [
                'symfonyMessage' => 'The image height is too small ({{ height }}px). Minimum height expected is {{ min_height }}px.',
                'symfonyParams' => ['{{ height }}' => 100, '{{ min_height }}' => 200],
                'expectedKey' => 'validator.image_height_too_small',
                'expectedIcuParams' => ['height' => 100, 'minHeight' => 200],
                'translatedMessage' => 'Výška obrázku je příliš malá (100px). Minimální očekávaná výška je 200px.',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $symfonyParams
     * @param array<string, mixed> $expectedIcuParams
     */
    #[DataProvider('fileMessagesProvider')]
    public function testTranslatesFileMessages(
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
     * @param array<string, mixed> $symfonyParams
     * @param array<string, mixed> $expectedIcuParams
     */
    #[DataProvider('imageMessagesProvider')]
    public function testTranslatesImageMessages(
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
