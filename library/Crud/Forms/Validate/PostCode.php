<?php

namespace Crud\Forms\Validate;

class PostCode extends \Zend_Validate_PostCode
{

    protected $_messageTemplates = array(
        self::INVALID  =>"Invalid type given, value must be string or integer",
        self::NO_MATCH => 
            "'%value%' does not appear to be an postal code. e.g: CR9 2ER",
    );

    public function isValid($value)
    {
        $value = strtoupper(str_replace(' ', '', $value));
        return parent::isValid($value);
    }
}
