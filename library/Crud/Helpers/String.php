<?php
/**
 * must define $_metadata and the ctor !!!
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
class Crud_Helpers_String
{

    /**
     *
     * @param int $bytes bytes (e.g. from filesize())
     * @return <type>
     */
    public static function bytesToHumanReadableFormat($bytes)
    {
        if ($bytes<1024) {
            return $bytes . ' bytes';
        } else if ($bytes<1024*1024) {
            return round($bytes/1024, 0) . ' Kb';
        } else {
            return round($bytes/1048576, 2) . ' Mb';
        }
    }

    public static function generateRandomString()
    {
        return md5(microtime(true).$_SERVER['REMOTE_ADDR']);
    }

    public static function isValidGeneratedRandomString($str)
    {
        return self::isMd5String($str);
    }

    public static function isMd5String($str)
    {
        return preg_match('/^[0-9a-f]{32}$/i', $str);
    }

    /**
     * Returns ??
     * @param string $stringToSplit
     * @param int $lengthFirstString
     * @return array 2 parts of the input string
     */
    public static function splitString($stringToSplit, $lengthFirstString)
    {        
        if (strlen($stringToSplit > $lengthFirstString)) {
            // find the position of the first space
            $pos = strpos($stringToSplit, ' ', $lengthFirstString);
    
            if ((strlen($stringToSplit) > $lengthFirstString) && $pos) {
                $s1 = substr($stringToSplit, 0, $pos);
                $s2 = substr($stringToSplit, $pos);
            } else {
                $s1 = $stringToSplit;
                $s2 = null;
            }
            
            return array($s1, $s2);
        } else {
            return array($stringToSplit, null);            
        }
    }

    /** Convert "camelCase Strings" to "camel Case  Strings"
     *
     * @param string $input
     * @return string
     */
    public static function camelCaseToSeparateStrings($input)
    {
        return preg_replace('/(?!^-)[[:upper:]]/', ' \0', $input);
    }

    /** Convert action name to human readable format
     * e.g:
     *
     * @param string $input
     * @return string
     */
    public static function actionNameToReadableString($input)
    {
        $ret = str_replace(array('-'), array(' '), self::camelCaseToSeparateStrings($input));
        $ret = ucfirst($ret);
        return $ret;
    }

}