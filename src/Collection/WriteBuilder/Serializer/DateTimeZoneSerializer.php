<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer;

use DateTime;
use DateTimeZone;
use Exception;
use Megio\Collection\Exception\SerializerException;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Serializer\Base\BaseSerializer;

class DateTimeZoneSerializer extends BaseSerializer
{
    public function serialize(IField $field): ?DateTime
    {
        $value = $field->getValue();

        if (!is_array($value)) {
            throw new SerializerException('Invalid value for DateTimeZoneSerializer');
        }

        if (!array_key_exists('datetime', $value) || !array_key_exists('zone', $value)) {
            throw new SerializerException('Invalid value for DateTimeZoneSerializer');
        }

        $dateTimeString = $value['datetime'];
        $zoneString = $value['zone'];

        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1]) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $dateTimeString)) {
            throw new SerializerException('Invalid value for DateTimeZoneSerializer');
        }

        $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
        $errors = DateTime::getLastErrors();

        if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            throw new SerializerException('Invalid value for DateTimeZoneSerializer');
        }

        try {
            $zone = new DateTimeZone($zoneString);
        } catch (Exception) {
            throw new SerializerException('Invalid value for DateTimeZoneSerializer');
        }

        if ($date instanceof DateTime) {
            $date->setTimezone($zone);
            return $date;
        }

        throw new SerializerException('Invalid value for DateTimeSerializer');
    }
}
