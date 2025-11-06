<?php
declare(strict_types=1);

namespace Megio\Http\Serializer;

use Megio\Http\Serializer\Validator\RecursiveValidator;
use Megio\Http\Serializer\Validator\ReflectionHelper;
use Megio\Http\Serializer\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;

class ValidatorFactory
{
    public static function create(): ValidatorInterface
    {
        $symfonyValidator = Validation::createValidator();
        $reflectionHelper = new ReflectionHelper();

        return new RecursiveValidator($symfonyValidator, $reflectionHelper);
    }
}
