<?php
declare(strict_types=1);

namespace Megio\Collection\Helper;

use Doctrine\DBAL\Types\Types;
use Megio\Collection\WriteBuilder\Field\ArrayField;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Field\Base\PureField;
use Megio\Collection\WriteBuilder\Field\DateField;
use Megio\Collection\WriteBuilder\Field\DateTimeField;
use Megio\Collection\WriteBuilder\Field\DateTimeIntervalField;
use Megio\Collection\WriteBuilder\Field\DecimalField;
use Megio\Collection\WriteBuilder\Field\EmailField;
use Megio\Collection\WriteBuilder\Field\IntegerField;
use Megio\Collection\WriteBuilder\Field\JsonField;
use Megio\Collection\WriteBuilder\Field\PasswordField;
use Megio\Collection\WriteBuilder\Field\PhoneCzField;
use Megio\Collection\WriteBuilder\Field\SlugField;
use Megio\Collection\WriteBuilder\Field\TextField;
use Megio\Collection\WriteBuilder\Field\TimeField;
use Megio\Collection\WriteBuilder\Field\ToggleBtnField;
use Megio\Collection\WriteBuilder\Field\UrlField;
use Megio\Collection\WriteBuilder\Field\VideoLinkField;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Nette\Utils\Strings;

class FieldCreator
{
    public static function create(WriteBuilder $builder, string $columnType, string $name, mixed $defaultValue): IField
    {
        $namesMap = [
            'password' => new PasswordField($name, $name, defaultValue: $defaultValue),
            'email' => new EmailField($name, $name, defaultValue: $defaultValue),
            'phone_cz' => new PhoneCzField($name, $name, defaultValue: $defaultValue),
            'slug' => new SlugField($name, $name, defaultValue: $defaultValue),
            'url' => new UrlField($name, $name, defaultValue: $defaultValue),
            'video' => new VideoLinkField($name, $name, defaultValue: $defaultValue),
        ];
        
        $fieldByName = null;
        foreach ($namesMap as $key => $field) {
            if (Strings::contains($name, $key)) {
                $fieldByName = $field;
            }
        }
        
        $instance = match ($columnType) {
            Types::ASCII_STRING,
            Types::BIGINT,
            Types::BINARY,
            Types::GUID,
            Types::STRING,
            Types::BLOB,
            Types::TEXT => $fieldByName ?: new TextField($name, $name, defaultValue: $defaultValue),
            
            Types::DECIMAL,
            Types::FLOAT => new DecimalField($name, $name, defaultValue: $defaultValue),
            
            'int',
            Types::INTEGER,
            Types::SMALLINT => new IntegerField($name, $name, defaultValue: $defaultValue),
            
            'bool',
            Types::BOOLEAN => new ToggleBtnField($name, $name, defaultValue: $defaultValue),
            
            Types::DATE_MUTABLE,
            Types::DATE_IMMUTABLE => new DateField($name, $name, defaultValue: $defaultValue),
            Types::DATEINTERVAL => new DateTimeIntervalField($name, $name, defaultValue: $defaultValue),
            
            Types::DATETIME_MUTABLE,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIMETZ_MUTABLE,
            Types::DATETIMETZ_IMMUTABLE => new DateTimeField($name, $name, defaultValue: $defaultValue),
            
            Types::JSON => new JsonField($name, $name),
            Types::SIMPLE_ARRAY => new ArrayField($name, $name, defaultValue: $defaultValue),
            
            Types::TIME_MUTABLE,
            Types::TIME_IMMUTABLE => new TimeField($name, $name, defaultValue: $defaultValue),
            
            default => new PureField($name, $name, defaultValue: $defaultValue),
        };
        
        $instance->setBuilder($builder);
        
        return $instance;
    }
}