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
class Crud_Forms_Filter_Helper  { //Crud_Forms_Filter_Helper


    public static function isFormPosted(Zend_Controller_Request_Http $request){
        return $request->isPost() && $request->getPost('form_filter_submitted',0);
   }

    /** Returns options (from the post) for the 3rd parameter of Crud_Model_DbTable_Abstract::fetchPaginatorAdapter
     *
     * @param array $post PostData, pass $this->getRequest()->getPost()
     *  e.g: array(
     *          form_filter_submitted = 1
     *          <columnsID1> => array( enabled=>0|1, mode=>equalto|differentfrom|contains|...|between, val=>string ),
     *          ...
     *          <columnsIDn> => array( enabled=>0|1, mode=>equalto|differentfrom|contains|...|between, val=>string ),
     *  )
     *
     * @return array    options or empty array
     */
   public static function arrayToWhereArray($post, Crud_Model_DbTable_Abstract $model){
       $ret = array();
       $post = self::removeDisabledOptions($post);
       $tableAlias = $model->getSqlPrefix();
       foreach ((array)$post as $field => $options) {
          if (is_array($options) /* && isset($options['enabled']) && $options['enabled']*/){
                $mode  = isset($options['mode'] ) ? $options['mode'] : null;
                $val  = isset($options['val']  ) ? $options['val']  : null;
                $val2  = isset($options['val2'] ) ? $options['val2'] : null;
                switch($mode){
                  case 'equalto':
                      $ret[$field] = array($tableAlias.$field.' = ?',$val);
                      break;
                  case 'differentfrom':
                      $ret[$field] = array($tableAlias.$field.' <> ?',$val);
                      break;
                  case 'contains':
                      $ret[$field] = $tableAlias.$field." LIKE '%$val%'";
                      break;
                  case 'lessthan':
                      $ret[$field] = array($tableAlias.$field.' < ?',$val);
                      break;
                  case 'greaterthan':
                      $ret[$field] = array($tableAlias.$field.' > ?',$val);
                      break;
                  case 'isnull':
                      $ret[$field] = $tableAlias.$field.' IS NULL';
                      break;
                  case 'isnotnull':
                      $ret[$field] = $tableAlias.$field.' IS NOT NULL';
                      break;
                  case 'between':
                      if($val2) {
                        $ret[$field] = $tableAlias.$field.' > '.$val.' AND '.$tableAlias.$field.' < '.$val2;
                      }
                      break;
                  /*default:   nth */
                }
          }
       }
       //pd($post,$ret);
       return $ret;
   }

   /**
    * Transform $_POST array to a string. It uses DASH to collapse keys. It skips disabled options
    * @param array $whereAr array posted from the filter form
    * [form_filter_submitted] => 1
      [id] => Array
        (
            [enabled] => 1
            [mode] => equalto
            [val] => 66
        )
      [email] => Array
        (
            [enabled] => 1
            [mode] => contains
            [val] => elvis
        )
      [submit] => Filter
    * @return string made using http_build_query
    *   id-enabled=1&id-mode=equalto&id-val=66&email-enabled=1&email-mode=contains&email-val=elvis
    */
   public static function arrayToString($whereAr){
       $queryParams = array();
       $whereAr = self::removeDisabledOptions($whereAr);
       foreach((array)$whereAr as $field => $options){
           if (is_array($options)){
              foreach((array)$options as $k=>$v){
                  if ($k!='enabled'){
                    $queryParams[$field.'-'.$k] = $v;
                  }
              }
           }
       }
       $ret = http_build_query($queryParams);
       //pd($whereAr,$queryParams, $ret);
       return $ret;
    }

    /** Removes the disabled options ('enabled' not true) from the array
     * @param array $ar
     * @return array
     */
    private static function removeDisabledOptions($ar){
        $ret = array();
        foreach($ar as $k => $options){
            if ( !is_array($options) || ( is_array($options) && isset($options['enabled']) && $options['enabled'] ) ) {
                $ret[$k] = $options;
            }
        }
        return $ret;
    }

    /** is _GET filter enabled
     *
     * @param <type> $request
     * @return boolean
     */
    public static function isFilterEnabled($request)
    {
        $filterArray = Crud_Forms_Filter_Helper::stringToArray(
            $request->getParam('filter', '')
        );

        //array not empty => filter enabled !
        return !empty($filterArray);
    }


    /** reads the $_GET string and return the array to populate the form and tranform to arrayWhere for the paginator
     *
     * @param <type> $string
     * @return int
     */
    public static function stringToArray($string){
        $queryParams = array();
        parse_str($string, $queryParams);
        $ret = array();
        foreach($queryParams as $k=>$v){
            list($column, $option) = preg_split ( '/-/' , $k );
            $ret[$column][$option] = $v;
            $ret[$column]['enabled'] = 1; //add enabled value as removed in the query string
        }
        $ret['form_filter_submitted'] = 1; //to complete the form to populate
        //pd($string,$ret);
        return $ret;
    }

    public static function stringToWhereArray($string){
        return self::arrayToWhereArray(self::stringToArray($string));
    }


   /** Update the $select object using the passed filter
    *
    * @param Zend_Paginator_Adapter_DbTableSelect $select
    * @param array $filters use  format array 2d, see other class methods
    * @return Zend_Paginator_Adapter_DbTableSelect
    */
   public static function updateSelect(Zend_Db_Table_Select $select, $filters){
       // add any filters which are set
        //$select->reset(Zend_Db_Table_Select::WHERE);
       
       #$schema = $select->getTable()->info(Zend_Db_Table_Abstract::NAME);
       #$prefix = $schema ? $schema.'.' : '';
       $prefix = ''; //TODO //should contain <tablealias>.  if there is a join
        foreach ((array)$filters as $currentFilter) { //Array (   [0] => id = ?     [1] => 1    )
            if (is_array($currentFilter)){
                $select->where($prefix.$currentFilter[0], $currentFilter[1]);
            } else {
                $select->where($currentFilter);
            }
        }
        //pd((string)$select);
        //pd($select);
        return $select;
   }

   public static function url($view, $post)
    {
        //pd($view, $post);
        return $view->url(
            array(
               'action'   => 'index',
               'page'     => 1, //reset page when changing order
               'filter'   => Crud_Forms_Filter_Helper::arrayToString($post),
            )
        );
    }
   
}