<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\FieldBuilder\Field\Select;
use Megio\Collection\FieldBuilder\Field\ToggleSwitch;
use Megio\Collection\FieldBuilder\Field\Text;
use Megio\Collection\FieldBuilder\FieldBuilder;
use Megio\Collection\FieldBuilder\Field\Email;
use Megio\Collection\FieldBuilder\Field\Password;
use Megio\Collection\FieldBuilder\Rule\AnyOfRule;
use Megio\Collection\FieldBuilder\Rule\DateCzRule;
use Megio\Collection\FieldBuilder\Rule\DateTimeCzRule;
use Megio\Collection\FieldBuilder\Rule\CzPhoneRule;
use Megio\Collection\FieldBuilder\Rule\DecimalRule;
use Megio\Collection\FieldBuilder\Rule\EqualRule;
use Megio\Collection\FieldBuilder\Rule\HourMinuteCzRule;
use Megio\Collection\FieldBuilder\Rule\IntegerRule;
use Megio\Collection\FieldBuilder\Rule\JsonRule;
use Megio\Collection\FieldBuilder\Rule\JsonStringRule;
use Megio\Collection\FieldBuilder\Rule\MaxRule;
use Megio\Collection\FieldBuilder\Rule\MinRule;
use Megio\Collection\FieldBuilder\Rule\NullableRule;
use Megio\Collection\FieldBuilder\Rule\NumericRule;
use Megio\Collection\FieldBuilder\Rule\RegexRule;
use Megio\Collection\FieldBuilder\Rule\RequiredRule;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\FieldBuilder\Rule\SlugRule;
use Megio\Collection\FieldBuilder\Rule\StringRule;
use Megio\Collection\FieldBuilder\Rule\TimeCzRule;
use Megio\Collection\FieldBuilder\Rule\UniqueRule;
use Megio\Collection\FieldBuilder\Rule\UrlRule;
use Megio\Collection\FieldBuilder\Rule\VideoLinkRule;
use Megio\Database\Entity\Admin;

class FieldRuleTestRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Admin::class;
    }
    
    public function name(): string
    {
        return 'field-rule-test';
    }
    
    public function readOne(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
    
    public function readAll(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
    
    public function create(FieldBuilder $builder): FieldBuilder
    {
        return $builder
            ->ignoreDoctrineRules()
            
            // Email and Password
            ->add(new Email('email', '', [
                new RequiredRule(),
                new UniqueRule($this->source(), 'email')
            ]))
            ->add(new Password('password', '', [
                new RequiredRule(),
                new NullableRule()
            ]))
            ->add(new Password('equal_to_password', '', [
                new EqualRule('password')
            ], [], false))
            
            // Select & anyOf
            ->add(new Select('any_of', '', [
                new Select\Item(0, 'Test_1'),
                new Select\Item(1, 'Test_2')
            ], [
                new NullableRule(),
                new IntegerRule(),
                new AnyOfRule([1, 2, 3])
            ], [], false))
            
            // URL
            ->add(new Text('url', '', [
                new RequiredRule(),
                new NullableRule(),
                new StringRule(),
                new UrlRule()
            ], [], false))
            
            // JSON
            ->add(new Text('json', '', [
                new RequiredRule(),
                new NullableRule(),
                new JsonRule(),
            ], [], false))
            
            // JSON String
            ->add(new Text('json_string', '', [
                new RequiredRule(),
                new NullableRule(),
                new JsonStringRule(),
            ], [], false))
            
            // Phone number
            ->add(new Text('cz_phone', '', [
                new RequiredRule(),
                new NullableRule(),
                new CzPhoneRule()
            ], [], false))
            
            // Slug
            ->add(new Text('slug', '', [
                new SlugRule(),
                new RequiredRule(),
                new NullableRule(),
            ], [], false))
            
            // Regex
            ->add(new Text('regex', '', [
                new RequiredRule(),
                new NullableRule(),
                new RegexRule('/^[a-z]+/')
            ], [], false))
            
            // Video link
            ->add(new Text('video_link', '', [
                new RequiredRule(),
                new NullableRule(),
                new VideoLinkRule()
            ], [], false))
            
            // Date, Time, Datetime
            ->add(new Text('date_time', '', [
                new RequiredRule(),
                new NullableRule(),
                new DateTimeCzRule()
            ], [], false))
            ->add(new Text('date', '', [
                new RequiredRule(),
                new NullableRule(),
                new DateCzRule()
            ], [], false))
            ->add(new Text('time', '', [
                new RequiredRule(),
                new NullableRule(),
                new TimeCzRule()
            ], [], false))
            ->add(new Text('hour_minute', '', [
                new RequiredRule(),
                new NullableRule(),
                new HourMinuteCzRule()
            ], [], false))
            
            // Toggle switch (boolean)
            ->add(new ToggleSwitch('bool_true', '', [], [], false))
            ->add(new ToggleSwitch('bool_null', '', [new NullableRule()], [], false))
            
            // Min and Max (number)
            ->add(new Text('num_min_5', '', [new MinRule(5)], [], false))
            ->add(new Text('num_max_5', '', [new MaxRule(5)], [], false))
            ->add(new Text('num_min_null', '', [new MinRule(5), new NullableRule()], [], false))
            ->add(new Text('num_max_null', '', [new MaxRule(5), new NullableRule()], [], false))
            
            // Min and Max (string)
            ->add(new Text('string_min_5', '', [new MinRule(5)], [], false))
            ->add(new Text('string_max_5', '', [new MaxRule(5)], [], false))
            ->add(new Text('string_min_null', '', [new MinRule(5), new NullableRule()], [], false))
            ->add(new Text('string_max_null', '', [new MaxRule(5), new NullableRule()], [], false))
            
            // Numerics
            ->add(new Text('numeric', '', [new NumericRule()], [], false))
            ->add(new Text('numeric_nullable', '', [new NumericRule(), new NullableRule()], [], false))
            ->add(new Text('numeric_required', '', [new NumericRule(), new RequiredRule()], [], false))
            
            // Decimals
            ->add(new Text('decimal', '', [], [], false))
            ->add(new Text('decimal_nullable', '', [new DecimalRule(), new NullableRule()], [], false))
            ->add(new Text('decimal_required', '', [new DecimalRule(), new RequiredRule()], [], false))
            
            // Integers
            ->add(new Text('integer', '', [new IntegerRule()], [], false))
            ->add(new Text('integer_nullable', '', [new IntegerRule(), new NullableRule()], [], false))
            ->add(new Text('integer_required', '', [new IntegerRule(), new RequiredRule()], [], false))
            
            // Strings
            ->add(new Text('string', '', [new StringRule()], [], false))
            ->add(new Text('string_nullable', '', [new StringRule(), new NullableRule()], [], false))
            ->add(new Text('string_required', '', [new StringRule(), new RequiredRule()], [], false));
    }
    
    public function update(FieldBuilder $builder): FieldBuilder
    {
        return $builder
            ->add(new Text('email', '', [
                new RequiredRule(),
                new UniqueRule($this->source(), 'email')
            ], [], true));
    }
}