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
class Ul extends \Zend_View_Helper_Abstract
{
    public function ul(/*array*/ $valuesKV, $startTag='<ul>', $endtag = '</ul>')
    {
        $ret = $startTag;
        foreach ((Array)$valuesKV as $k => $v) {
            $ret .= sprintf(
                '<li>%s%s</li>',
                (is_int($k) || !$k) ? '' : '<b>'.$k.'</b>:',
                $v ? $v : '-'
            );
        }
        $ret .= '</ul>';
        return $ret;
    }
}