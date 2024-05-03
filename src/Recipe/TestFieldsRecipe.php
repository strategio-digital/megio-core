<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\CollectionRecipe;
use Megio\Collection\CollectionRequest;
use Megio\Collection\WriteBuilder\Field\ArrayField;
use Megio\Collection\WriteBuilder\Field\DateCzField;
use Megio\Collection\WriteBuilder\Field\DateField;
use Megio\Collection\WriteBuilder\Field\DateTimeCzField;
use Megio\Collection\WriteBuilder\Field\DateTimeField;
use Megio\Collection\WriteBuilder\Field\DateTimeIntervalField;
use Megio\Collection\WriteBuilder\Field\DateTimeZoneField;
use Megio\Collection\WriteBuilder\Field\DecimalField;
use Megio\Collection\WriteBuilder\Field\EmailField;
use Megio\Collection\WriteBuilder\Field\HiddenField;
use Megio\Collection\WriteBuilder\Field\HourMinuteCzField;
use Megio\Collection\WriteBuilder\Field\HourMinuteField;
use Megio\Collection\WriteBuilder\Field\IntegerField;
use Megio\Collection\WriteBuilder\Field\JsonField;
use Megio\Collection\WriteBuilder\Field\NumericField;
use Megio\Collection\WriteBuilder\Field\PasswordField;
use Megio\Collection\WriteBuilder\Field\PhoneCzField;
use Megio\Collection\WriteBuilder\Field\RichTextField;
use Megio\Collection\WriteBuilder\Field\SelectField;
use Megio\Collection\WriteBuilder\Field\SlugField;
use Megio\Collection\WriteBuilder\Field\TextAreaField;
use Megio\Collection\WriteBuilder\Field\TextField;
use Megio\Collection\WriteBuilder\Field\TimeCzField;
use Megio\Collection\WriteBuilder\Field\TimeField;
use Megio\Collection\WriteBuilder\Field\ToggleBtnField;
use Megio\Collection\WriteBuilder\Field\UrlField;
use Megio\Collection\WriteBuilder\Field\VideoLinkField;
use Megio\Collection\WriteBuilder\Rule\NullableRule;
use Megio\Collection\WriteBuilder\Rule\RequiredRule;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Database\Entity\Admin;
use Nette\Schema\Expect;

class TestFieldsRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Admin::class;
    }
    
    public function key(): string
    {
        return 'test-fields';
    }
    
    public function create(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        $items = [
            new SelectField\Item(0, 'Test_1'),
            new SelectField\Item(1, 'Test_2')
        ];
        
        $schema = Expect::structure([
            'email' => Expect::email()->required(),
            'name' => Expect::string()->required()->min(3)->max(32),
        ]);
        
        return $builder
            ->add(new HiddenField('hidden', 'Hidden', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false, defaultValue: 'yep'))
            ->add(new ArrayField('array', 'Array', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new DateCzField('date_cz', 'Date CZ', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new DateField('date', 'Date', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new DateTimeCzField('date_time_cz', 'DateTime CZ', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new DateTimeField('date_time', 'DateTime', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new DateTimeIntervalField('date_time_interval', 'DateTime interval', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new DateTimeZoneField('date_time_zone', 'DateTime zone', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new DecimalField('decimal', 'Decimal', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new EmailField('email', 'E-mail', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new HourMinuteCzField('hour_minute_cz', 'HourMinute CZ', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new HourMinuteField('hour_minute', 'HourMinute', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new IntegerField('integer', 'Integer', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new JsonField('json', 'JSON', schema: $schema, rules: [new NullableRule(), new RequiredRule()], attrs: ['fullWidth' => true], mapToEntity: false))
            ->add(new NumericField('numeric', 'Numeric', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new PasswordField('password', 'Password', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new PhoneCzField('phone_cz', 'Phone CZ', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new RichTextField('rich_text', 'RichText', rules: [new NullableRule(), new RequiredRule()], attrs: ['fullWidth' => true], mapToEntity: false))
            ->add(new SelectField('select', 'Select', items: $items, rules: [new NullableRule(), new RequiredRule()], mapToEntity: false, defaultValue: $items[0]->getValue()))
            ->add(new RichTextField('rich_text_2', 'RichText', rules: [new NullableRule(), new RequiredRule()], attrs: ['fullWidth' => true], mapToEntity: false, defaultValue: null))
            ->add(new SlugField('slug', 'Slug', slugFrom: 'email', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new TextAreaField('text_area', 'Text area', rules: [new NullableRule(), new RequiredRule()], attrs: ['fullWidth' => true], mapToEntity: false))
            ->add(new TextField('text', 'Text', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new TimeCzField('time_cz', 'Time CZ', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new TimeField('time', 'Time', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new ToggleBtnField('toggle_btn', 'Toggle Btn', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new UrlField('url', 'URL', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false))
            ->add(new VideoLinkField('video_link', 'Video link', rules: [new NullableRule(), new RequiredRule()], mapToEntity: false));
    }
}