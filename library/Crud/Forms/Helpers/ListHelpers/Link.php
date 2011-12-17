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
class Link extends \Zend_View_Helper_Abstract
{
    public function link(
        $link, $len=55, $displayName=null, $target = '_blank',
        $displayErrors = true
    )
    {
        if ($displayName==null) {
            $displayName = (strlen($link)>$len) ?
                         substr($link, 0, $len) . '...' : $link;
        }

        $error = '';
        if ($displayErrors) {
            if ($link) {
                if (!\Zend_Uri::check($link)) {
                    $error = 'URL NOT VALID';
                }
            } else {
                $error = '-';
            }
            $error = '<span class="errors">' . $error . '</span>';
        }

        return sprintf(
            '<a href="%s" title="%s" target="%s">%s</a>%s',
            $link, // link
            $link, // title
            $target, // _blank
            $displayName, // text
            $error
        );
    }
}