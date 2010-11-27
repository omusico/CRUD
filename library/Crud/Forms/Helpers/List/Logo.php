<?php
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
class Crud_Forms_Helpers_List_Logo extends Zend_View_Helper_Abstract {
    public function logo($imgSrc, $defaultW = 100, $defaultH = 100){
        list($w, $h) = @getimagesize($imgSrc);
        return sprintf('<img src="%s" width="%d" heigth="%d">',
                $imgSrc,
                $w ? $w:$defaultW,
                $h ? $h:$defaultH
             );
    }
}