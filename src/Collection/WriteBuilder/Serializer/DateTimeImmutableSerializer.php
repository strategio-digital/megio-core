<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer;

use Megio\Collection\Exception\SerializerException;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Serializer\Base\BaseSerializer;

class DateTimeImmutableSerializer extends BaseSerializer
{
    public function serialize(IField $field): ?\DateTimeImmutable
    {
        $value = $field->getValue();
        
        if ($value === null){
            return null;
        }
        
        if (!is_string($value)) {
            throw new SerializerException('Invalid value for DateTimeImmutableSerializer');
        }
        
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1]) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $value)) {
            throw new SerializerException('Invalid value for DateTimeImmutableSerializer');
        }
        
        $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
        $errors = \DateTimeImmutable::getLastErrors();
        
        if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            throw new SerializerException('Invalid value for DateTimeImmutableSerializer');
        }
        
        if ($date instanceof \DateTimeImmutable) {
            return $date;
        }
        
        throw new SerializerException('Invalid value for DateTimeImmutableSerializer');
    }
}