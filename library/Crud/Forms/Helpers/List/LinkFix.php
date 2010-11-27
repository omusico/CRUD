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
class Crud_Forms_Helpers_List_LinkFix extends Zend_View_Helper_Abstract {
    public function linkFix($link, $len=55){

        //add http prefix if not existing
        $linkFixed = false;
        $link = trim($link);
        if (substr($link, 0, 4) != 'http') {
            $link = 'http://' . $link;
            $linkFixed = true;
        }

        $truncatedLink = (strlen($link)>$len) ? substr($link, 0, $len) . '...' : $link;
        return sprintf('<a href="%s" title="%s" target="blank">%s</a>%s %s',
                $link, //link
                $link,  //title
                $truncatedLink, //text
                $linkFixed ? ' [prefix added]' : '',
                ($link) ? (Zend_Uri::check($link)?'':'<span class="errors">URL NOT VALID</span>') :'<span class="errors">none</span>'
                );
    }
}