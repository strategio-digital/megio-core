<?php

namespace Megio\Collection\FieldBuilder\Field\Base;

enum FieldNativeType: string
{
    case CUSTOM = '@custom';
    case HIDDEN = 'hidden';
    
    case EMAIL = 'email';
    case FILE = 'file';
    
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case PASSWORD = 'password';
    case NUMBER = 'number';
    
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
}
