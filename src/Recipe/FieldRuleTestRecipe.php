<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\FieldBuilder\Field\ArrayField;
use Megio\Collection\FieldBuilder\Field\Decimal;
use Megio\Collection\FieldBuilder\Field\Integer;
use Megio\Collection\FieldBuilder\Field\Json;
use Megio\Collection\FieldBuilder\Field\Numeric;
use Megio\Collection\FieldBuilder\Field\Select;
use Megio\Collection\FieldBuilder\Field\Slug;
use Megio\Collection\FieldBuilder\Field\TextArea;
use Megio\Collection\FieldBuilder\Field\ToggleSwitch;
use Megio\Collection\FieldBuilder\Field\Text;
use Megio\Collection\FieldBuilder\FieldBuilder;
use Megio\Collection\FieldBuilder\Field\Email;
use Megio\Collection\FieldBuilder\Field\Password;
use Megio\Collection\FieldBuilder\Rule\AnyOfRule;
use Megio\Collection\FieldBuilder\Rule\DateCzRule;
use Megio\Collection\FieldBuilder\Rule\DateTimeCzRule;
use Megio\Collection\FieldBuilder\Rule\PhoneCzRule;
use Megio\Collection\FieldBuilder\Rule\EqualRule;
use Megio\Collection\FieldBuilder\Rule\HourMinuteCzRule;
use Megio\Collection\FieldBuilder\Rule\IntegerRule;
use Megio\Collection\FieldBuilder\Rule\JsonRule;
use Megio\Collection\FieldBuilder\Rule\JsonStringRule;
use Megio\Collection\FieldBuilder\Rule\MaxRule;
use Megio\Collection\FieldBuilder\Rule\MinRule;
use Megio\Collection\FieldBuilder\Rule\NullableRule;
use Megio\Collection\FieldBuilder\Rule\RegexRule;
use Megio\Collection\FieldBuilder\Rule\RequiredRule;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\FieldBuilder\Rule\SlugRule;
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
        $items = [
            new Select\Item(0, 'Test_1'),
            new Select\Item(1, 'Test_2')
        ];
        
        return $builder
            ->ignoreDoctrineRules()
            
            // Email and Password
            ->add(new Email('email', '', [new RequiredRule(), new UniqueRule($this->source(), 'email')]))
            ->add(new Password('password', '', [new RequiredRule(), new NullableRule()]))
            ->add(new Password('equal_to_password', '', [new EqualRule('password')], [], false))
            
            // URL, phone, video
            ->add(new Text('url', '', [new RequiredRule(), new NullableRule(), new UrlRule()], [], false))
            ->add(new Text('cz_phone', '', [new RequiredRule(), new NullableRule(), new PhoneCzRule()], [], false))
            ->add(new Text('video_link', '', [new RequiredRule(), new NullableRule(), new VideoLinkRule()], [], false))
            
            // JSON & JSON String
            ->add(new Json('json', '', [new RequiredRule(), new NullableRule()], [], false))
            ->add(new TextArea('json_string', '', [new RequiredRule(), new NullableRule(), new JsonStringRule()], [], false))
            
            // AnyOf, Slug, Regex
            ->add(new Select('any_of', '', $items, [new NullableRule(), new IntegerRule(), new AnyOfRule([1, 2, 3])], [], false))
            ->add(new Slug('slug', '', [new RequiredRule(), new NullableRule()], [], false))
            ->add(new Text('regex', '', [new RequiredRule(), new NullableRule(), new RegexRule('/^[a-z]+/')], [], false))
            
            // Date, Time, Datetime
            ->add(new Text('date_time', '', [new RequiredRule(), new NullableRule(), new DateTimeCzRule()], [], false))
            ->add(new Text('date', '', [new RequiredRule(), new NullableRule(), new DateCzRule()], [], false))
            ->add(new Text('time', '', [new RequiredRule(), new NullableRule(), new TimeCzRule()], [], false))
            ->add(new Text('hour_minute', '', [new RequiredRule(), new NullableRule(), new HourMinuteCzRule()], [], false))
            
            // Toggle switch (boolean)
            ->add(new ToggleSwitch('bool_true', '', [], [], false))
            ->add(new ToggleSwitch('bool_null', '', [new NullableRule()], [], false))
            
            // Min/Max (numeric)
            ->add(new Numeric('min_num_5', '', [new MinRule(5)], [], false))
            ->add(new Numeric('min_num_null', '', [new MinRule(5), new NullableRule()], [], false))
            ->add(new Numeric('max_num_5', '', [new MaxRule(5)], [], false))
            ->add(new Numeric('max_num_null', '', [new MaxRule(5), new NullableRule()], [], false))
            
            // Min/Max (string)
            ->add(new Text('min_string_5', '', [new MinRule(5)], [], false))
            ->add(new Text('min_string_null', '', [new MinRule(5), new NullableRule()], [], false))
            ->add(new Text('max_string_5', '', [new MaxRule(5)], [], false))
            ->add(new Text('max_string_null', '', [new MaxRule(5), new NullableRule()], [], false))
            
            // Min/Max (array)
            ->add(new ArrayField('min_array_5', '', [new MinRule(5)], [], false))
            ->add(new ArrayField('min_array_null', '', [new MinRule(5), new NullableRule()], [], false))
            ->add(new ArrayField('max_array_5', '', [new MaxRule(5)], [], false))
            ->add(new ArrayField('max_array_null', '', [new MaxRule(5), new NullableRule()], [], false))
            
            // Strings
            ->add(new Text('string', '', [], [], false))
            ->add(new Text('string_nullable', '', [new NullableRule()], [], false))
            ->add(new Text('string_required', '', [new RequiredRule()], [], false))
            
            // Numerics
            ->add(new Numeric('numeric', '', [], [], false))
            ->add(new Numeric('numeric_nullable', '', [new NullableRule()], [], false))
            ->add(new Numeric('numeric_required', '', [new RequiredRule()], [], false))
            
            // Integers
            ->add(new Integer('integer', '', [new IntegerRule()], [], false))
            ->add(new Integer('integer_nullable', '', [new NullableRule()], [], false))
            ->add(new Integer('integer_required', '', [new RequiredRule()], [], false))
            
            // Decimals
            ->add(new Decimal('decimal', '', [], [], false))
            ->add(new Decimal('decimal_nullable', '', [new NullableRule()], [], false))
            ->add(new Decimal('decimal_required', '', [new RequiredRule()], [], false));
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