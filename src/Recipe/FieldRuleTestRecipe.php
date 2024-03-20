<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\FieldBuilder\Field\ArrayField;
use Megio\Collection\FieldBuilder\Field\DecimalField;
use Megio\Collection\FieldBuilder\Field\IntegerField;
use Megio\Collection\FieldBuilder\Field\JsonField;
use Megio\Collection\FieldBuilder\Field\NumericField;
use Megio\Collection\FieldBuilder\Field\SelectField;
use Megio\Collection\FieldBuilder\Field\SlugField;
use Megio\Collection\FieldBuilder\Field\TextAreaField;
use Megio\Collection\FieldBuilder\Field\ToggleBtnField;
use Megio\Collection\FieldBuilder\Field\TextField;
use Megio\Collection\FieldBuilder\FieldBuilder;
use Megio\Collection\FieldBuilder\Field\EmailField;
use Megio\Collection\FieldBuilder\Field\PasswordField;
use Megio\Collection\FieldBuilder\Rule\AnyOfRule;
use Megio\Collection\FieldBuilder\Rule\DateCzRule;
use Megio\Collection\FieldBuilder\Rule\DateTimeCzRule;
use Megio\Collection\FieldBuilder\Rule\PhoneCzRule;
use Megio\Collection\FieldBuilder\Rule\EqualRule;
use Megio\Collection\FieldBuilder\Rule\HourMinuteCzRule;
use Megio\Collection\FieldBuilder\Rule\IntegerRule;
use Megio\Collection\FieldBuilder\Rule\JsonStringRule;
use Megio\Collection\FieldBuilder\Rule\MaxRule;
use Megio\Collection\FieldBuilder\Rule\MinRule;
use Megio\Collection\FieldBuilder\Rule\NullableRule;
use Megio\Collection\FieldBuilder\Rule\RegexRule;
use Megio\Collection\FieldBuilder\Rule\RequiredRule;
use Megio\Collection\CollectionRecipe;
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
            new SelectField\Item(0, 'Test_1'),
            new SelectField\Item(1, 'Test_2')
        ];
        
        return $builder
            ->ignoreDoctrineRules()
            
            // Email and Password
            ->add(new EmailField('email', '', [new RequiredRule(), new UniqueRule($this->source(), 'email')]))
            ->add(new PasswordField('password', '', [new RequiredRule(), new NullableRule()]))
            ->add(new PasswordField('equal_to_password', '', [new EqualRule('password')], [], false))
            
            // URL, phone, video
            ->add(new TextField('url', '', [new RequiredRule(), new NullableRule(), new UrlRule()], [], false))
            ->add(new TextField('cz_phone', '', [new RequiredRule(), new NullableRule(), new PhoneCzRule()], [], false))
            ->add(new TextField('video_link', '', [new RequiredRule(), new NullableRule(), new VideoLinkRule()], [], false))
            
            // JSON & JSON String
            ->add(new JsonField('json', '', [new RequiredRule(), new NullableRule()], [], false))
            ->add(new TextAreaField('json_string', '', [new RequiredRule(), new NullableRule(), new JsonStringRule()], [], false))
            
            // AnyOf, Slug, Regex
            ->add(new SelectField('any_of', '', $items, [new NullableRule(), new IntegerRule(), new AnyOfRule([1, 2, 3])], [], false))
            ->add(new SlugField('slug', '', [new RequiredRule(), new NullableRule()], [], false))
            ->add(new TextField('regex', '', [new RequiredRule(), new NullableRule(), new RegexRule('/^[a-z]+/')], [], false))
            
            // Date, Time, Datetime
            ->add(new TextField('date_time', '', [new RequiredRule(), new NullableRule(), new DateTimeCzRule()], [], false))
            ->add(new TextField('date', '', [new RequiredRule(), new NullableRule(), new DateCzRule()], [], false))
            ->add(new TextField('time', '', [new RequiredRule(), new NullableRule(), new TimeCzRule()], [], false))
            ->add(new TextField('hour_minute', '', [new RequiredRule(), new NullableRule(), new HourMinuteCzRule()], [], false))
            
            // Toggle switch (boolean)
            ->add(new ToggleBtnField('bool_true', '', [], [], false))
            ->add(new ToggleBtnField('bool_null', '', [new NullableRule()], [], false))
            
            // Min/Max (numeric)
            ->add(new NumericField('min_num_5', '', [new MinRule(5)], [], false))
            ->add(new NumericField('min_num_null', '', [new MinRule(5), new NullableRule()], [], false))
            ->add(new NumericField('max_num_5', '', [new MaxRule(5)], [], false))
            ->add(new NumericField('max_num_null', '', [new MaxRule(5), new NullableRule()], [], false))
            
            // Min/Max (string)
            ->add(new TextField('min_string_5', '', [new MinRule(5)], [], false))
            ->add(new TextField('min_string_null', '', [new MinRule(5), new NullableRule()], [], false))
            ->add(new TextField('max_string_5', '', [new MaxRule(5)], [], false))
            ->add(new TextField('max_string_null', '', [new MaxRule(5), new NullableRule()], [], false))
            
            // Min/Max (array)
            ->add(new ArrayField('min_array_5', '', [new MinRule(5)], [], false))
            ->add(new ArrayField('min_array_null', '', [new MinRule(5), new NullableRule()], [], false))
            ->add(new ArrayField('max_array_5', '', [new MaxRule(5)], [], false))
            ->add(new ArrayField('max_array_null', '', [new MaxRule(5), new NullableRule()], [], false))
            
            // Strings
            ->add(new TextField('string', '', [], [], false))
            ->add(new TextField('string_nullable', '', [new NullableRule()], [], false))
            ->add(new TextField('string_required', '', [new RequiredRule()], [], false))
            
            // Numerics
            ->add(new NumericField('numeric', '', [], [], false))
            ->add(new NumericField('numeric_nullable', '', [new NullableRule()], [], false))
            ->add(new NumericField('numeric_required', '', [new RequiredRule()], [], false))
            
            // Integers
            ->add(new IntegerField('integer', '', [new IntegerRule()], [], false))
            ->add(new IntegerField('integer_nullable', '', [new NullableRule()], [], false))
            ->add(new IntegerField('integer_required', '', [new RequiredRule()], [], false))
            
            // Decimals
            ->add(new DecimalField('decimal', '', [], [], false))
            ->add(new DecimalField('decimal_nullable', '', [new NullableRule()], [], false))
            ->add(new DecimalField('decimal_required', '', [new RequiredRule()], [], false));
    }
    
    public function update(FieldBuilder $builder): FieldBuilder
    {
        return $builder
            ->add(new TextField('email', '', [
                new RequiredRule(),
                new UniqueRule($this->source(), 'email')
            ], [], true));
    }
}