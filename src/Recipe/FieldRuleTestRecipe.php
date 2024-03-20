<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\WriteBuilder\Field\ArrayField;
use Megio\Collection\WriteBuilder\Field\DateCzField;
use Megio\Collection\WriteBuilder\Field\DateTimeCzField;
use Megio\Collection\WriteBuilder\Field\DecimalField;
use Megio\Collection\WriteBuilder\Field\HourMinuteCzField;
use Megio\Collection\WriteBuilder\Field\IntegerField;
use Megio\Collection\WriteBuilder\Field\JsonField;
use Megio\Collection\WriteBuilder\Field\NumericField;
use Megio\Collection\WriteBuilder\Field\PhoneCzField;
use Megio\Collection\WriteBuilder\Field\SelectField;
use Megio\Collection\WriteBuilder\Field\SlugField;
use Megio\Collection\WriteBuilder\Field\TextAreaField;
use Megio\Collection\WriteBuilder\Field\TimeCzField;
use Megio\Collection\WriteBuilder\Field\ToggleBtnField;
use Megio\Collection\WriteBuilder\Field\TextField;
use Megio\Collection\WriteBuilder\Field\UrlField;
use Megio\Collection\WriteBuilder\Field\VideoLinkField;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\Field\EmailField;
use Megio\Collection\WriteBuilder\Field\PasswordField;
use Megio\Collection\WriteBuilder\Rule\AnyOfRule;
use Megio\Collection\WriteBuilder\Rule\EqualRule;
use Megio\Collection\WriteBuilder\Rule\IntegerRule;
use Megio\Collection\WriteBuilder\Rule\JsonStringRule;
use Megio\Collection\WriteBuilder\Rule\MaxRule;
use Megio\Collection\WriteBuilder\Rule\MinRule;
use Megio\Collection\WriteBuilder\Rule\NullableRule;
use Megio\Collection\WriteBuilder\Rule\RegexRule;
use Megio\Collection\WriteBuilder\Rule\RequiredRule;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\WriteBuilder\Rule\UniqueRule;
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
    
    public function showOne(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
    
    public function showAll(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
    
    public function create(WriteBuilder $builder): WriteBuilder
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
    
    public function update(WriteBuilder $builder): WriteBuilder
    {
        return $builder
            ->add(new TextField('email', '', [
                new RequiredRule(),
                new UniqueRule($this->source(), 'email')
            ], [], false, true));
    }
}