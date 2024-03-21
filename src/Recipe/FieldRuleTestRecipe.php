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
    
    public function create(WriteBuilder $builder): WriteBuilder
    {
        $items = [
            new SelectField\Item(0, 'Test_1'),
            new SelectField\Item(1, 'Test_2')
        ];
        
        return $builder
            ->ignoreDoctrineRules()
            
            // Email and Password
            ->add(new EmailField(name: 'email', label: '', rules: [new RequiredRule(), new UniqueRule($this->source(), 'email')]))
            ->add(new PasswordField(name: 'password', label: '', rules: [new RequiredRule(), new NullableRule()]))
            ->add(new PasswordField(name: 'equal_to_password', label: '', rules: [new EqualRule('password')], mapToEntity: false))
            
            // URL, phone, video
            ->add(new UrlField(name: 'url', label: '', rules: [new RequiredRule(), new NullableRule()], mapToEntity: false))
            ->add(new PhoneCzField(name: 'phone_cz', label: '', rules: [new RequiredRule(), new NullableRule()], mapToEntity: false))
            ->add(new VideoLinkField(name: 'video_link', label: '', rules: [new RequiredRule(), new NullableRule()], mapToEntity: false))
            
            // JSON & JSON String
            ->add(new JsonField(name: 'json', label: '', rules: [new RequiredRule(), new NullableRule()], mapToEntity: false))
            ->add(new TextAreaField(name: 'json_string', label: '', rules: [new RequiredRule(), new NullableRule(), new JsonStringRule()], mapToEntity: false))
            
            // AnyOf, Slug, Regex
            ->add(new SelectField(name: 'any_of', label: '', items: $items, rules: [new NullableRule(), new IntegerRule(), new AnyOfRule([1, 2, 3])], mapToEntity: false))
            ->add(new SlugField(name: 'slug', label: '', rules: [new RequiredRule(), new NullableRule()], mapToEntity: false))
            ->add(new TextField(name: 'regex', label: '', rules: [new RequiredRule(), new NullableRule(), new RegexRule('/^[a-z]+/')], mapToEntity: false))
            
            // Date, Time, Datetime
            ->add(new DateTimeCzField(name: 'date_time', label: '', rules: [new RequiredRule(), new NullableRule()], mapToEntity: false))
            ->add(new DateCzField(name: 'date', label: '', rules: [new RequiredRule(), new NullableRule()], mapToEntity: false))
            ->add(new TimeCzField(name: 'time', label: '', rules: [new RequiredRule(), new NullableRule()], mapToEntity: false))
            ->add(new HourMinuteCzField(name: 'hour_minute', label: '', rules: [new RequiredRule(), new NullableRule()], mapToEntity: false))
            
            // Toggle switch (boolean)
            ->add(new ToggleBtnField(name: 'bool_true', label: '', mapToEntity: false))
            ->add(new ToggleBtnField(name: 'bool_null', label: '', rules: [new NullableRule()], mapToEntity: false))
            
            // Min/Max (numeric)
            ->add(new NumericField(name: 'min_num_5', label: '', rules: [new MinRule(5)], mapToEntity: false))
            ->add(new NumericField(name: 'min_num_null', label: '', rules: [new MinRule(5), new NullableRule()], mapToEntity: false))
            ->add(new NumericField(name: 'max_num_5', label: '', rules: [new MaxRule(5)], mapToEntity: false))
            ->add(new NumericField(name: 'max_num_null', label: '', rules: [new MaxRule(5), new NullableRule()], mapToEntity: false))
            
            // Min/Max (string)
            ->add(new TextField(name: 'min_string_5', label: '', rules: [new MinRule(5)], mapToEntity: false))
            ->add(new TextField(name: 'min_string_null', label: '', rules: [new MinRule(5), new NullableRule()], mapToEntity: false))
            ->add(new TextField(name: 'max_string_5', label: '', rules: [new MaxRule(5)], mapToEntity: false))
            ->add(new TextField(name: 'max_string_null', label: '', rules: [new MaxRule(5), new NullableRule()], mapToEntity: false))
            
            // Min/Max (array)
            ->add(new ArrayField(name: 'min_array_5', label: '', rules: [new MinRule(5)], mapToEntity: false))
            ->add(new ArrayField(name: 'min_array_null', label: '', rules: [new MinRule(5), new NullableRule()], mapToEntity: false))
            ->add(new ArrayField(name: 'max_array_5', label: '', rules: [new MaxRule(5)], mapToEntity: false))
            ->add(new ArrayField(name: 'max_array_null', label: '', rules: [new MaxRule(5), new NullableRule()], mapToEntity: false))
            
            // Strings
            ->add(new TextField(name: 'string', label: '', mapToEntity: false))
            ->add(new TextField(name: 'string_nullable', label: '', rules: [new NullableRule()], mapToEntity: false))
            ->add(new TextField(name: 'string_required', label: '', rules: [new RequiredRule()], mapToEntity: false))
            
            // Numerics
            ->add(new NumericField(name: 'numeric', label: '', mapToEntity: false))
            ->add(new NumericField(name: 'numeric_nullable', label: '', rules: [new NullableRule()], mapToEntity: false))
            ->add(new NumericField(name: 'numeric_required', label: '', rules: [new RequiredRule()], mapToEntity: false))
            
            // Integers
            ->add(new IntegerField(name: 'integer', label: '', rules: [new IntegerRule()], mapToEntity: false))
            ->add(new IntegerField(name: 'integer_nullable', label: '', rules: [new NullableRule()], mapToEntity: false))
            ->add(new IntegerField(name: 'integer_required', label: '', rules: [new RequiredRule()], mapToEntity: false))
            
            // Decimals
            ->add(new DecimalField(name: 'decimal', label: '', mapToEntity: false))
            ->add(new DecimalField(name: 'decimal_nullable', label: '', rules: [new NullableRule()], mapToEntity: false))
            ->add(new DecimalField(name: 'decimal_required', label: '', rules: [new RequiredRule()], mapToEntity: false));
    }
    
    public function update(WriteBuilder $builder): WriteBuilder
    {
        return $builder
            ->add(new TextField(name: 'email', label: '', rules: [
                new RequiredRule(),
                new UniqueRule($this->source(), 'email')
            ], mapToEntity: true));
    }
}