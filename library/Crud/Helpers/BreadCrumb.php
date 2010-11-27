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
class Crud_Helpers_BreadCrumb
{
    const REGISTRY_ID = '_breadcrumb';
    const SEPARATOR = ' &gt; ';


    private static function getValFromRegistry()
    {
        try {
            $ar = Zend_Registry::get(self::REGISTRY_ID);
        } catch (Zend_Exception $e) {
            // 1st time: set 
            Zend_Registry::set(self::REGISTRY_ID, array());
            $ar = Zend_Registry::get(self::REGISTRY_ID);
        }
        return (array)$ar;
    }

    public static function hasContent()
    {
        return count(self::getValFromRegistry()) > 0;
    }

    public static function reset()
    {
         Zend_Registry::set(self::REGISTRY_ID, array());
    }

    /** prints the breadcrumbs bt reading registry
     *
     * @param string $currentSection current index
     * @param string $separator separator
     * @return string  html bar
     */
    public static function printBar()
    {
        $ret = array();
        $ar = self::getValFromRegistry();
        foreach ((array)$ar as $name => $options) {
            list($link, $current) = $options;
            if ($current) {
                $ret[] = $name;
            } else {
                $ret[] = sprintf('<a href="%s">%s</a>', $link, $name);
            }
        }
        return implode(self::SEPARATOR, $ret);
    }

    
    /** Add a link to the breadcrumb
     *
     * @param string $name printed name
     * @param string $link  link
     */
    public static function addLink($name, $link, $current = false)
    {
        $ar = self::getValFromRegistry();
        //set current to false to all the other links
        if ($current) {
            foreach ($ar as &$v) {
                $v[1] = false;
            }
        }
        $ar[$name] = array($link, $current);
        $ar = Zend_Registry::set(self::REGISTRY_ID, $ar);
    }

    
    /** remove link from the menu
     *
     * @param int $name elem array index (link printed name)
     */
    public static function removeLink($index)
    {
        $ar = self::getValFromRegistry();
        unset($ar[$index]);
        $ar = Zend_Registry::set(self::REGISTRY_ID, $ar);
    }

}