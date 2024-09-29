<?php
declare(strict_types=1);

namespace Megio\Collection\Helper;

use Doctrine\DBAL\Types\Types;
use Megio\Collection\ReadBuilder\Column\ArrayColumn;
use Megio\Collection\ReadBuilder\Column\Base\IColumn;
use Megio\Collection\ReadBuilder\Column\BlobColumn;
use Megio\Collection\ReadBuilder\Column\BooleanColumn;
use Megio\Collection\ReadBuilder\Column\DateColumn;
use Megio\Collection\ReadBuilder\Column\DateTimeColumn;
use Megio\Collection\ReadBuilder\Column\DateTimeIntervalColumn;
use Megio\Collection\ReadBuilder\Column\EmailColumn;
use Megio\Collection\ReadBuilder\Column\JsonColumn;
use Megio\Collection\ReadBuilder\Column\NumericColumn;
use Megio\Collection\ReadBuilder\Column\PhoneColumn;
use Megio\Collection\ReadBuilder\Column\StringColumn;
use Megio\Collection\ReadBuilder\Column\TimeColumn;
use Megio\Collection\ReadBuilder\Column\UnknownColumn;
use Megio\Collection\ReadBuilder\Column\UrlColumn;
use Megio\Collection\ReadBuilder\Column\VideoLinkColumn;
use Nette\Utils\Strings;

class ColumnCreator
{
    public static function create(string $type, string $key, bool $visible, bool $sortable): IColumn
    {
        $keysMap = [
            'email' => new EmailColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            'phone' => new PhoneColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            'url' => new UrlColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            'video' => new VideoLinkColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
        ];
        
        $columnByKey = null;
        foreach ($keysMap as $colKey => $column) {
            if (Strings::contains($key, $colKey)) {
                $columnByKey = $column;
            }
        }
        
        return match ($type) {
            Types::ASCII_STRING,
            Types::BIGINT,
            Types::BINARY,
            Types::DECIMAL,
            Types::GUID,
            Types::STRING,
            Types::TEXT => $columnByKey ?: new StringColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            
            Types::BLOB => new BlobColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            
            Types::BOOLEAN => new BooleanColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            
            Types::DATE_MUTABLE,
            Types::DATE_IMMUTABLE => new DateColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            Types::DATEINTERVAL => new DateTimeIntervalColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            
            Types::DATETIME_MUTABLE,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIMETZ_MUTABLE,
            Types::DATETIMETZ_IMMUTABLE => new DateTimeColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            
            'int',
            Types::FLOAT,
            Types::INTEGER,
            Types::SMALLINT => new NumericColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            
            Types::JSON => new JsonColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            Types::SIMPLE_ARRAY => new ArrayColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            
            Types::TIME_MUTABLE,
            Types::TIME_IMMUTABLE => new TimeColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
            
            default => new UnknownColumn(key: $key, name: $key, sortable: $sortable, visible: $visible),
        };
    }
}