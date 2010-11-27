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
class Crud_Forms_Helpers_List_LinkIframe extends Zend_View_Helper_Abstract
{
    public function linkIframe($src, $linkText, $options)
    {
       $target = isset($options['target']) ? $options['target'] : '_blank';
       $w = isset($options['width']) ? $options['width'] : 740;
       $h = isset($options['height']) ? $options['height'] : 300;
       $pk = isset($options['pk']) ? $options['pk'] : '';
       $frame_id = isset($options['frame_id']) ? $options['frame_id'] : '';
       $colspan = isset($options['colspan']) ? $options['colspan'] : 6;
       $iframeUniqueId = 'frame'.$frame_id.'record'.$pk;
       $jsFunctionName = 'openifr' . $pk .''. md5($src);

       return  '<script type="text/javascript" language="javascript">
               function '.$jsFunctionName.'() {
                   if ($("#addptrow'.$iframeUniqueId.'").length==0) {
                      $("body").append(\'<div id="waitoverlay"><div>Loading...<br><img src="/images/admin_layout/loading300.gif" /></div></div>\');
                      $("#waitoverlay").click(function(){
                          $("#waitoverlay").remove();
                      })
                      $(".internaliframe").remove();
                      if ($("#ptrow'.$pk.'").length==0) { alert("Unable to find the row. pk parameter of linkframe helper not valid "); }
                      $("#ptrow'.$pk.'").after(\'<tr id="addptrow'.$iframeUniqueId.'" class="internaliframe"><td></td><td colspan="'.$colspan.'" id="addptrowiframe'.$iframeUniqueId.'" class="internaliframe">Loading...</td></tr>\');
                      $("#addptrowiframe'.$iframeUniqueId.'").html(\'<iframe src="' . $src . '" width="' . $w . '" height="' . $h . '" frameborder="0" class="internal" scrollbars="yes" id="iframe'.$iframeUniqueId.'" />\');
                      $("#iframe'.$iframeUniqueId.'").load(function(){
                          $("#waitoverlay").remove();
                      })
                   } else {
                      $(".internaliframe").remove();
                   }
               }
               </script>'
               . '<a href="' . $src . '" title="' . strip_tags($linkText)
               . '" target="' . $target . '" onclick="'.$jsFunctionName.'();return false;">'
               . $linkText
               . '</a>';
    }
}