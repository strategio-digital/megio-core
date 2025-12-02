<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Serializer\ValidatorTranslatorAdapter;

use Megio\Http\Serializer\ValidatorMessageMap;
use Megio\Http\Serializer\ValidatorTranslatorAdapter;
use Megio\Translation\Translator;
use PHPUnit\Framework\TestCase;

class FallbackBehaviorTest extends TestCase
{
    public function testFallbackToOriginalMessageWhenTranslationNotFound(): void
    {
        $unknownMessage = 'Some unknown validation message.';

        $translator = $this->createMock(Translator::class);
        $translator
            ->expects($this->once())
            ->method('translate')
            ->with($unknownMessage, [])
            ->willReturn($unknownMessage);

        $adapter = new ValidatorTranslatorAdapter($translator);
        $result = $adapter->trans($unknownMessage);

        $this->assertSame($unknownMessage, $result);
    }

    public function testFallbackWithSymfonyPlaceholders(): void
    {
        $unknownMessage = 'Unknown message with {{ value }}.';
        $params = ['{{ value }}' => 'test'];

        $translator = $this->createMock(Translator::class);
        $translator
            ->expects($this->once())
            ->method('translate')
            ->with($unknownMessage, ['value' => 'test'])
            ->willReturn($unknownMessage);

        $adapter = new ValidatorTranslatorAdapter($translator);
        $result = $adapter->trans($unknownMessage, $params);

        $this->assertSame('Unknown message with test.', $result);
    }

    public function testGetLocaleReturnsPosixFromTranslator(): void
    {
        $translator = $this->createMock(Translator::class);
        $translator
            ->expects($this->once())
            ->method('getPosix')
            ->willReturn('cs_CZ');

        $adapter = new ValidatorTranslatorAdapter($translator);
        $result = $adapter->getLocale();

        $this->assertSame('cs_CZ', $result);
    }

    public function testAllMappedMessagesExistInValidatorMessageMap(): void
    {
        $this->assertArrayHasKey('This value should not be blank.', ValidatorMessageMap::MAP);
        $this->assertArrayHasKey('This value is not a valid email address.', ValidatorMessageMap::MAP);
        $this->assertArrayHasKey(
            'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.',
            ValidatorMessageMap::MAP,
        );
        $this->assertArrayHasKey(
            'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
            ValidatorMessageMap::MAP,
        );
        $this->assertArrayHasKey(
            'You must select at least {{ limit }} choice.|You must select at least {{ limit }} choices.',
            ValidatorMessageMap::MAP,
        );
        $this->assertArrayHasKey(
            'This collection should contain {{ limit }} element or more.|This collection should contain {{ limit }} elements or more.',
            ValidatorMessageMap::MAP,
        );
    }
}
