<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\FieldBuilder\Field\ArrayField;
use Megio\Collection\FieldBuilder\Field\DateCzField;
use Megio\Collection\FieldBuilder\Field\DateTimeCzField;
use Megio\Collection\FieldBuilder\Field\DecimalField;
use Megio\Collection\FieldBuilder\Field\HourMinuteCzField;
use Megio\Collection\FieldBuilder\Field\IntegerField;
use Megio\Collection\FieldBuilder\Field\JsonField;
use Megio\Collection\FieldBuilder\Field\NumericField;
use Megio\Collection\FieldBuilder\Field\PhoneCzField;
use Megio\Collection\FieldBuilder\Field\SelectField;
use Megio\Collection\FieldBuilder\Field\SlugField;
use Megio\Collection\FieldBuilder\Field\TextAreaField;
use Megio\Collection\FieldBuilder\Field\TimeCzField;
use Megio\Collection\FieldBuilder\Field\ToggleBtnField;
use Megio\Collection\FieldBuilder\Field\TextField;
use Megio\Collection\FieldBuilder\Field\UrlField;
use Megio\Collection\FieldBuilder\Field\VideoLinkField;
use Megio\Collection\FieldBuilder\FieldBuilder;
use Megio\Collection\FieldBuilder\Field\EmailField;
use Megio\Collection\FieldBuilder\Field\PasswordField;
use Megio\Collection\FieldBuilder\Rule\AnyOfRule;
use Megio\Collection\FieldBuilder\Rule\EqualRule;
use Megio\Collection\FieldBuilder\Rule\IntegerRule;
use Megio\Collection\FieldBuilder\Rule\JsonStringRule;
use Megio\Collection\FieldBuilder\Rule\MaxRule;
use Megio\Collection\FieldBuilder\Rule\MinRule;
use Megio\Collection\FieldBuilder\Rule\NullableRule;
use Megio\Collection\FieldBuilder\Rule\RegexRule;
use Megio\Collection\FieldBuilder\Rule\RequiredRule;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\FieldBuilder\Rule\UniqueRule;
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
            ->add(new PasswordField('equal_to_password', '', [new EqualRule('password')], [], false, false))
            
            // URL, phone, video
            ->add(new UrlField('url', '', [new RequiredRule(), new NullableRule()], [], false, false))
            ->add(new PhoneCzField('phone_cz', '', [new RequiredRule(), new NullableRule()], [], false, false))
            ->add(new VideoLinkField('video_link', '', [new RequiredRule(), new NullableRule()], [], false, false))
            
            // JSON & JSON String
            ->add(new JsonField('json', '', [new RequiredRule(), new NullableRule()], [], false, false))
            ->add(new TextAreaField('json_string', '', [new RequiredRule(), new NullableRule(), new JsonStringRule()], [], false, false))
            
            // AnyOf, Slug, Regex
            ->add(new SelectField('any_of', '', $items, [new NullableRule(), new IntegerRule(), new AnyOfRule([1, 2, 3])], [], false, false))
            ->add(new SlugField('slug', '', [new RequiredRule(), new NullableRule()], [], false, false))
            ->add(new TextField('regex', '', [new RequiredRule(), new NullableRule(), new RegexRule('/^[a-z]+/')], [], false, false))
            
            // Date, Time, Datetime
            ->add(new DateTimeCzField('date_time', '', [new RequiredRule(), new NullableRule()], [], false, false))
            ->add(new DateCzField('date', '', [new RequiredRule(), new NullableRule()], [], false, false))
            ->add(new TimeCzField('time', '', [new RequiredRule(), new NullableRule()], [], false, false))
            ->add(new HourMinuteCzField('hour_minute', '', [new RequiredRule(), new NullableRule()], [], false, false))
            
            // Toggle switch (boolean)
            ->add(new ToggleBtnField('bool_true', '', [], [], false, false))
            ->add(new ToggleBtnField('bool_null', '', [new NullableRule()], [], false, false))
            
            // Min/Max (numeric)
            ->add(new NumericField('min_num_5', '', [new MinRule(5)], [], false, false))
            ->add(new NumericField('min_num_null', '', [new MinRule(5), new NullableRule()], [], false, false))
            ->add(new NumericField('max_num_5', '', [new MaxRule(5)], [], false, false))
            ->add(new NumericField('max_num_null', '', [new MaxRule(5), new NullableRule()], [], false, false))
            
            // Min/Max (string)
            ->add(new TextField('min_string_5', '', [new MinRule(5)], [], false, false))
            ->add(new TextField('min_string_null', '', [new MinRule(5), new NullableRule()], [], false, false))
            ->add(new TextField('max_string_5', '', [new MaxRule(5)], [], false, false))
            ->add(new TextField('max_string_null', '', [new MaxRule(5), new NullableRule()], [], false, false))
            
            // Min/Max (array)
            ->add(new ArrayField('min_array_5', '', [new MinRule(5)], [], false, false))
            ->add(new ArrayField('min_array_null', '', [new MinRule(5), new NullableRule()], [], false, false))
            ->add(new ArrayField('max_array_5', '', [new MaxRule(5)], [], false, false))
            ->add(new ArrayField('max_array_null', '', [new MaxRule(5), new NullableRule()], [], false, false))
            
            // Strings
            ->add(new TextField('string', '', [], [], false, false))
            ->add(new TextField('string_nullable', '', [new NullableRule()], [], false, false))
            ->add(new TextField('string_required', '', [new RequiredRule()], [], false, false))
            
            // Numerics
            ->add(new NumericField('numeric', '', [], [], false, false))
            ->add(new NumericField('numeric_nullable', '', [new NullableRule()], [], false, false))
            ->add(new NumericField('numeric_required', '', [new RequiredRule()], [], false, false))
            
            // Integers
            ->add(new IntegerField('integer', '', [new IntegerRule()], [], false, false))
            ->add(new IntegerField('integer_nullable', '', [new NullableRule()], [], false, false))
            ->add(new IntegerField('integer_required', '', [new RequiredRule()], [], false, false))
            
            // Decimals
            ->add(new DecimalField('decimal', '', [], [], false, false))
            ->add(new DecimalField('decimal_nullable', '', [new NullableRule()], [], false, false))
            ->add(new DecimalField('decimal_required', '', [new RequiredRule()], [], false, false));
    }
    
    public function update(FieldBuilder $builder): FieldBuilder
    {
        return $builder
            ->add(new TextField('email', '', [
                new RequiredRule(),
                new UniqueRule($this->source(), 'email')
            ], [], false, true));
    }
}