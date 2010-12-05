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
class Crud_Helpers_Array
{

    /** Convert
     * Array
     *   ([0] => Array([id] => 27        [name] => cars)
     *     [1] => Array([id] => 28        [name] => home) )
     *
     *  TO
     * Array
     *   ([27] => cars => 27        [28] => home))
     *  
     *
     * @return <type>
     */
    public static function fetchAllToAr(
        array $inputAr, $keyElem = 'id', $valueElem = 'name'
    )
    {
        $ret = array();
        foreach ($inputAr as $row) {
            //$ret[$row['id']] = $row['name']; // $ret[27] = 'cars';
            $ret[$row[$keyElem]] = $row[$valueElem];
        }
        return $ret;
    }



}