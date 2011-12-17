<?php

namespace Crud\Forms\Helpers\ListHelpers;

/**
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
class SubStr
    extends \Zend_View_Helper_Abstract
{
    public function subStr($val, $len = 150)
    {
        return (strlen($val)>$len) 
               ? substr($val, 0, $len) . ' <b>...</b>'
               : $val;
    }
}