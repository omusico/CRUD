<?php
/**
 * Config class
 * //TODO move to INI settings
 *
 * @category  Crud class
 * @package   Crud
 * @author    elvis ciotti <elvis@phpntips.com>
 * @copyright 2010 Phpntips.com
 * @license   http://framework.zend.com/license/new-bsd  New BSD License
 * @version   Release: 1.0
 * @link      http://www.phpntips.com/crud
 */
class Crud_Config
{

    const OPTION_JS_BASE_PATH = 'js_base_path';
    const OPTION_CSS_BASE_PATH = 'css_base_path';

    const PK_NAME = 'pk'; //name of URL parameter used to identify records

    //TODO: read config if exists

    public static function getOption($option)
    {
        $vals = array(
            self::OPTION_CSS_BASE_PATH => '/public/css/',
            self::OPTION_JS_BASE_PATH  => '/public/js/',
        );
        
        return isset($vals[$option]) ? $vals[$option] : 'option not valid';

    }

}