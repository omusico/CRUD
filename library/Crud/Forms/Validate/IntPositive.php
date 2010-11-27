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
class Crud_Forms_Validate_IntPositive extends Zend_Validate_Int
{
    
    protected $_messageTemplates = array(
        self::INVALID => "Invalid type given, value should be a integer greater than 0",
        self::NOT_INT => "'%value%' does not appear to be an integer greater than 0",
    );
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid integer AND greater than zero
     *
     * @param  string|integer $value
     * @return boolean
     */
    public function isValid($value)
    {

        if (!preg_match('/^[0-9]+$/', $value) || $value < 1) {
            $this->_error(self::INVALID);
            return false;
        }

        return parent::isValid($value);
    }

}