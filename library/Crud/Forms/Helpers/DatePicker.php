<?php

namespace Crud\Forms\Helpers;

/**
 * Helper for autocomplete fields (jquery)
 *//**
 * Class Name
 *
 * @category  Crud class
 * @package   Crud
 * @author    elvis ciotti <elvis@phpntips.com>
 * @copyright 2010 Phpntips.com
 * @license   http://framework.zend.com/license/new-bsd  New BSD License
 * @version   Release: 1.0
 * @link      http://www.phpntips.com/crud
 */
class DatePicker
{
 
    public static function getJs($field)
    {
        return 'datepicker_ymd("' . $field . '");';
    }

    public static function getJsTimestamp($field)
    {
        return 'datepickertimestamp("' . $field . '");';
    }
   
}