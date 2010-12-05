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
     * Convert bytes to human readablef ormat. eg 23,4 Mb
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

    /**
     * generate random string using md5, microtime, server addr
     *
     * @return string
     */
    public static function generateRandomString()
    {
        return md5(microtime(true).$_SERVER['REMOTE_ADDR']);
    }

    /**
     * @see isMd5String
     */
    public static function isValidGeneratedRandomString($str)
    {
        return self::isMd5String($str);
    }

    /**
     * check if a string can be a result of a md5
     *
     * @param string $str
     * @return int
     */
    public static function isMd5String($str)
    {
        return preg_match('/^[0-9a-f]{32}$/i', $str);
    }

    /**
     * Split string into 2 [...]
     * 
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
                $sOne = substr($stringToSplit, 0, $pos);
                $sTwo = substr($stringToSplit, $pos);
            } else {
                $sOne = $stringToSplit;
                $sTwo = null;
            }
            
            return array($sOne, $sTwo);
        } else {
            return array($stringToSplit, null);            
        }
    }

    /**
     * Convert "camelCase Strings" to "camel Case  Strings"
     *
     * @param string $input
     * @return string
     */
    public static function camelCaseToSeparateStrings($input)
    {
        return preg_replace('/(?!^-)[[:upper:]]/', ' \0', $input);
    }

    /**
     * Convert action name to human readable format
     * e.g:
     *
     * @param string $input
     * @return string
     */
    public static function actionNameToReadableString($input)
    {
        $ret = str_replace(
            array('-'),
            array(' '),
            self::camelCaseToSeparateStrings($input)
        );
        $ret = ucfirst($ret);
        return $ret;
    }

}