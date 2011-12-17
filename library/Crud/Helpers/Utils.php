<?php

namespace Crud\Helpers;

/**
 * various static methods
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
class Utils
{
    
    public static function getUserIdOrZero()
    {
        $user = \Zend_Auth::getInstance()->getIdentity();
        return ($user && is_array($user) && isset($user['id']))
               ? $user['id'] : 0;
    }


    /**
     *  Clean a string, leaving only aplhanumeric chars and spaces
     *
     * @param string $q
     * @return string
     */
    public static function cleanQuerySearch($q)
    {
        return preg_replace('/[^\w+] /', '', $q);
    }

}