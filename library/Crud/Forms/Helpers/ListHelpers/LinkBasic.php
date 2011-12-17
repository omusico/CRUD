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
class LinkBasic extends \Zend_View_Helper_Abstract
{
    public function linkBasic(
        $link, $displayName, $target = '_blank', $popupOptions = 0
    )
    {
        $popupHtml = "onclick=\"window.open('".$link
                   . "', '', 'scrollbars=yes, resizable=yes, status=no,"
                   . " location=no, toolbar=no, width=800, height=500');"
                   . "return false;\"";

        return sprintf(
            '<a %s href="%s" title="%s" target="%s">%s</a>',
            $popupOptions ? $popupHtml : '',
            $link, // link
            str_replace('"', ' ', strip_tags($displayName)),
            $target,
            $displayName
        );
    }
}