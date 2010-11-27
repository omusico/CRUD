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
class Crud_Helpers_Csv
{

    /** Returns CSV file
     *
     * @param array $titles
     * @param array $data array of arrays
     * @param string $sep
     * @param string $delim
     * @return string
     */
    public static function arrayToCsv($titles, $data, $sep = ',', $delim = '"')
    {
        if (!$titles || !$data) { return ''; }
        $ret = '';
        //merge rows
        array_unshift($data, $titles);
         //foreach data row
        foreach ($data as $row) {
            $cols = array();
            foreach ($row as $col) {
                $cols[] = $delim . str_replace($delim, '\\'.$delim, $col) . $delim;
            }
            $ret .= implode($sep, $cols) . PHP_EOL;
        }
        #pd($ret);
        return $ret;
    }

}