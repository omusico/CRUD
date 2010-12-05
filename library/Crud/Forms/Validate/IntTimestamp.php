<?php
/**
 * Validator for integer used as timestamp
 *
 * @category  Crud class
 * @package   Crud
 * @author    elvis ciotti <elvis@phpntips.com>
 * @copyright 2010 Phpntips.com
 * @license   http://framework.zend.com/license/new-bsd  New BSD License
 * @version   Release: 1.0
 * @link      http://www.phpntips.com/crud
 */
class Crud_Forms_Validate_IntTimestamp extends Zend_Validate_Int
{
    CONST MIN_YEAR = 1975; //1/1/1990 = mktime(0, 0, 0, 1, 1, 1990);
    protected $_messageTemplates = array(
        self::INVALID => "Invalid type given, value not a timestamp" ,
        self::NOT_INT => "'%value%' does not appear to be a timestamp"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid integer AND greater
     * than zero
     *
     * @param  string|integer $value
     * @return boolean
     */
    public function isValid($value)
    {
        if ($value < mktime(0, 0, 0, 1, 1, self::MIN_YEAR)) {
            $this->_error(self::INVALID);
            return false;
        }

        return parent::isValid($value);
    }

}