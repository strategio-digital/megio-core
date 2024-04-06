<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer;

use Megio\Collection\Exception\SerializerException;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Serializer\Base\BaseSerializer;

class DateTimeSerializer extends BaseSerializer
{
    public function serialize(IField $field): ?\DateTime
    {
        $value = $field->getValue();
        
        if ($value === null){
            return null;
        }
        
        if (!is_string($value)) {
            throw new SerializerException('Invalid value for DateTimeSerializer');
        }
        
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1]) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $value)) {
            throw new SerializerException('Invalid value for DateTimeSerializer');
        }
        
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        $errors = \DateTime::getLastErrors();
        
        if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            throw new SerializerException('Invalid value for DateTimeSerializer');
        }
        
        if ($date instanceof \DateTime) {
            return $date;
        }
        
        throw new SerializerException('Invalid value for DateTimeSerializer');
    }
}