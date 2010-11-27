<?php
class Crud_Forms_Validate_PostCode extends Zend_Validate_PostCode
{

    protected $_messageTemplates = array(
        self::INVALID  => "Invalid type given, value should be string or integer",
        self::NO_MATCH => "'%value%' does not appear to be an postal code. Use upper case format. e.g: CR9 2ER",
    );

    public function isValid($value)
    {
        $value = strtoupper(str_replace(' ','', $value));
        return parent::isValid($value);
    }
}
