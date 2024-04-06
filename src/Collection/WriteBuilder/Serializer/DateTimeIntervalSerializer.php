<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer;

use Megio\Collection\Exception\SerializerException;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Serializer\Base\BaseSerializer;

class DateTimeIntervalSerializer extends BaseSerializer
{
    public function serialize(IField $field): ?\DateInterval
    {
        $value = $field->getValue();
        
        if ($value === null){
            return null;
        }
        
        if (!is_string($value)) {
            throw new SerializerException('Invalid value for DateTimeIntervalSerializer');
        }
        
        $dateTimeRx = '\d{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1]) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])';
        
        if (!preg_match("/^($dateTimeRx) \- ($dateTimeRx)$/", $value, $matches)) {
            throw new SerializerException('Invalid value for DateTimeIntervalSerializer');
        }
        
        $from = \DateTime::createFromFormat('Y-m-d H:i:s', $matches[1]);
        $fromErrors = \DateTime::getLastErrors();
        
        $to = \DateTime::createFromFormat('Y-m-d H:i:s', $matches[7]);
        $toErrors = \DateTime::getLastErrors();
        
        if ($fromErrors && ($fromErrors['warning_count'] > 0 || $fromErrors['error_count'] > 0)) {
            throw new SerializerException('Invalid value for DateTimeIntervalSerializer');
        }
        
        if ($toErrors && ($toErrors['warning_count'] > 0 || $toErrors['error_count'] > 0)) {
            throw new SerializerException('Invalid value for DateTimeIntervalSerializer');
        }
        
        if ($from instanceof \DateTime && $to instanceof \DateTime) {
            return $from->diff($to);
        }
        
        throw new SerializerException('Invalid value for DateTimeIntervalSerializer');
    }
}