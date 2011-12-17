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
class DashIfEmpty extends \Zend_View_Helper_Abstract
{
    public function dashIfEmpty($array, $val, $replaceText = '-')
    {
        return (isset($array[$val]) && $array[$val]) 
               ? $array[$val]
               : $replaceText;
    }
}