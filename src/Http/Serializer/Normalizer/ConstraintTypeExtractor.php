<?php
declare(strict_types=1);

namespace Megio\Http\Serializer\Normalizer;

use ReflectionClass;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Type as TypeConstraint;

use function class_exists;
use function is_array;
use function is_string;

/**
 * Extracts array element types from #[Assert\All([new Assert\Type(ClassName::class)])] attributes.
 * Used by ObjectNormalizer to denormalize arrays of DTOs.
 */
final readonly class ConstraintTypeExtractor implements PropertyTypeExtractorInterface
{
    /**
     * @param class-string $class
     * @param array<string, mixed> $context
     */
    public function getType(string $class, string $property, array $context = []): ?Type
    {
        $reflection = new ReflectionClass($class);

        if ($reflection->hasProperty($property) === false) {
            return null;
        }

        $reflectionProperty = $reflection->getProperty($property);

        foreach ($reflectionProperty->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();

            if (($instance instanceof All) === false) {
                continue;
            }

            $constraints = $instance->constraints;

            if (is_array($constraints) === false) {
                continue;
            }

            foreach ($constraints as $constraint) {
                if (($constraint instanceof TypeConstraint) === false) {
                    continue;
                }

                if (is_string($constraint->type) === false) {
                    continue;
                }

                if (class_exists($constraint->type) === false) {
                    continue;
                }

                return Type::list(Type::object($constraint->type));
            }
        }

        return null;
    }
}
