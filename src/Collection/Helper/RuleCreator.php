<?php
declare(strict_types=1);

namespace Megio\Collection\Helper;

use Doctrine\DBAL\Types\Types;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Rule\ArrayRule;
use Megio\Collection\WriteBuilder\Rule\Base\IRule;
use Megio\Collection\WriteBuilder\Rule\BooleanRule;
use Megio\Collection\WriteBuilder\Rule\DateRule;
use Megio\Collection\WriteBuilder\Rule\DateTimeIntervalRule;
use Megio\Collection\WriteBuilder\Rule\DateTimeRule;
use Megio\Collection\WriteBuilder\Rule\DecimalRule;
use Megio\Collection\WriteBuilder\Rule\IntegerRule;
use Megio\Collection\WriteBuilder\Rule\JsonRule;
use Megio\Collection\WriteBuilder\Rule\MaxRule;
use Megio\Collection\WriteBuilder\Rule\NullableRule;
use Megio\Collection\WriteBuilder\Rule\StringRule;
use Megio\Collection\WriteBuilder\Rule\TimeRule;
use Megio\Collection\WriteBuilder\Rule\UniqueRule;

class RuleCreator
{
    public static function create(string $columnType): ?IRule
    {
        return match ($columnType) {
            Types::ASCII_STRING,
            Types::BIGINT,
            Types::BINARY,
            Types::GUID,
            Types::STRING,
            Types::BLOB,
            Types::TEXT => new StringRule(),
            
            Types::DECIMAL,
            Types::FLOAT => new DecimalRule(),
            
            Types::BOOLEAN => new BooleanRule(),
            
            Types::DATE_MUTABLE,
            Types::DATE_IMMUTABLE => new DateRule(),
            Types::DATEINTERVAL => new DateTimeIntervalRule(),
            
            Types::DATETIME_MUTABLE,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIMETZ_MUTABLE,
            Types::DATETIMETZ_IMMUTABLE => new DateTimeRule(),
            
            Types::INTEGER,
            Types::SMALLINT => new IntegerRule(),
            
            Types::JSON => new JsonRule(),
            Types::SIMPLE_ARRAY => new ArrayRule(),
            
            Types::TIME_MUTABLE,
            Types::TIME_IMMUTABLE => new TimeRule(),
            
            default => null,
        };
    }
    
    /**
     * @param \Megio\Collection\WriteBuilder\Field\Base\IField $field
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param array{
     *     name: string,
     *     type: string,
     *     unique: bool,
     *     nullable: bool,
     *     maxLength: int|null,
     *     defaultValue: mixed|\Megio\Collection\WriteBuilder\Field\Base\UndefinedValue
     * } $columnSchema
     * @return IField
     */
    public static function createRulesByDbSchema(IField $field, ICollectionRecipe $recipe, array $columnSchema): IField
    {
        $ruleClassNames = array_map(fn($rule) => $rule::class, $field->getRules());
        $rule = RuleCreator::create($columnSchema['type']);
        
        if ($rule !== null && !in_array($rule::class, $ruleClassNames)) {
            $field->addRule($rule);
        }
        
        if ($columnSchema['nullable'] === true && !in_array(NullableRule::class, $ruleClassNames)) {
            $field->addRule(new NullableRule());
        }
        
        if ($columnSchema['maxLength'] !== null && !in_array(MaxRule::class, $ruleClassNames)) {
            $field->addRule(new MaxRule($columnSchema['maxLength']));
        }
        
        if ($columnSchema['unique'] === true && !in_array(UniqueRule::class, $ruleClassNames)) {
            $field->addRule(new UniqueRule($recipe->source(), $field->getName()));
        }
        
        return $field;
    }
}