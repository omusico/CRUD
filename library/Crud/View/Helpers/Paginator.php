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
class Crud_View_Helpers_Paginator
{ //ex Crud_Helpers_Utils

    //ex Crud_View_Helpers_Paginator::paginatorToArray -> Crud_View_Helpers_Paginator::paginatorToArray
    public static function paginatorToArray(Zend_Paginator $paginator)
    {
        $res = array();
        foreach($paginator as $p){
            $res[] = $p->toArray();
        }
        return $res;
    }

    public static function getPKValue(array $row, $pk)
    {
       if (is_array($pk)) {
           foreach($pk as $k) {
                $kValues[$k] = $row[$k];
           }
           $ret = http_build_query($kValues);
           //pd($ret);
           return $ret;
       } else {
           return $row[$pk];
       }
        
    }

     //ex Crud_View_Helpers_Paginator::paginatorToTable -> Crud_View_Helpers_Paginator::paginatorToTable
    /** Print the listing table
     *
     * @param array|Zend_Paginator $dataOrPaginator input Data (Array 2d)
     * @param Zend_View $view
     * @param array $fields
     * @param array $options boolean keys: massActions|hideEditLink|hideDeleteLink
     * @return string html of the page
     */
    public static function paginatorToTable(
        $dataOrPaginator,
        Zend_View $view,
        $fields = array(),
        $options = array(),
        Crud_Model_Interface $model = null
    )
    {
        //define
        $pkFieldName = 'id';

        
        if ($model) {
            $pkFieldName = $model->getPKName();
        } //pd($pkFieldName);
        
        $pkURLName = Crud_Config::PK_NAME; //'pk';
        //$massActions = isset($options['massActions']) && $options['massActions'];
        $hideEditLink = isset($options['hideEditLink']) && $options['hideEditLink'];
        $hideDeleteLink = isset($options['hideDeleteLink']) && $options['hideDeleteLink'];
        $noTrJsToggle = isset($options['noTrJsToggle']) && $options['noTrJsToggle'];
        
        if ($dataOrPaginator instanceof Zend_Paginator){
            $dataOrPaginator = self::paginatorToArray($dataOrPaginator);
        }
        $ret = '';
        //$ret .= '<style type="text/css">#paginatorTable tr.clicked td { background: '.Crud_Config::BACKGROUND_PAGINATOR_TOGGLE_ROW.'; }</style>';
        //$ret  = '<form action="'.$view->url(array('action'=>'delete', 'id'=>'all')).'" method="post">';
        $ret .= '<table cellspacing=0 cellpadding=0 id="paginatorTable"><tr>';
        /*if ($massActions) {
            $ret .= '<th></th>';
        }*/
        //titles
        $i=1;
        foreach ($fields as $k=>$v) {
            $ret .= sprintf('<th %s>%s</th>', ($i++==0) ? 'class="first"' : '',  $v);
        }
        $ret .=  '<th nowrap="nowrap" class="last"></th></tr>';
        $i = 0;
        //foreach record/row
        foreach ($dataOrPaginator as $elem) {
            $pkValue = self::getPKValue($elem, $pkFieldName);

            $ret .= '<tr class="' . (($i%2)==0?'odd':'even') . '" id="ptrow'.$pkValue.'">';
            /*if ($massActions) {
                $ret .= '<td><input type="checkbox" name="record[]" value="'.self::getPKValue($elem, $pkFieldName).'" /></td>';
            }*/
            //foreach column
            foreach ($fields as $k=>$v) {
                    $ret .= '<td >';
                    $ret .= $elem[$k];
                    $ret .= '</td>';
            }
            $ret .= '<td nowrap="nowrap" class="last">';
            if (!$hideEditLink) {
                $ret .= '[ <a href="'.$view->url(array('action'=>'edit', $pkURLName=>$pkValue)).'">Edit</a> ]<br/>';
            }
            if (!$hideDeleteLink) {
                $ret .= '[ <a href="'.$view->url(array('action'=>'delete', $pkURLName=>$pkValue)).'">Delete</a> ]';
            }
            $ret .= '</td></tr>';
            $i++;
        }
        /*if ($massActions) {
            $ret .= '<tr><td><input type="checkbox" id="checkbox_all" ></td>';
            $ret .= '<td colspan='.(count($fields)+1).'>';
            $ret .= '<select name="action"><option></option><option>delete</option></select><input type="submit" value="do">';
            $ret .= '</td></tr>';
        }*/
        $ret .= '</table>';
        if (!$noTrJsToggle) {
            $ret .= '<script type="text/javascript">
            $().ready(function(){
                $("#paginatorTable tr").click(function(){
                   $(this).toggleClass(\'clicked\');
                });
            });
            </script>';
        }


        //$ret .= '</form>';
        return $ret;
    }
}