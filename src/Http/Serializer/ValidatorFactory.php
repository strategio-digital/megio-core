<?php

declare(strict_types=1);

namespace Megio\Http\Serializer;

use Megio\Http\Serializer\Validator\RecursiveValidator;
use Megio\Http\Serializer\Validator\ReflectionHelper;
use Megio\Http\Serializer\Validator\ValidatorInterface;
use Megio\Translation\Translator;
use Symfony\Component\Validator\Validation;

class ValidatorFactory
{
    public static function create(Translator $translator): ValidatorInterface
    {
        $translatorAdapter = new ValidatorTranslatorAdapter($translator);

        $symfonyValidator = Validation::createValidatorBuilder()
            ->setTranslator($translatorAdapter)
            ->getValidator();

        $reflectionHelper = new ReflectionHelper();

        return new RecursiveValidator($symfonyValidator, $reflectionHelper);
    }
}
