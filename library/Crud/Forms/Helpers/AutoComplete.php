<?php
/**
 * Helper for autocomplete fields (jquery)
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
class Crud_Forms_Helpers_AutoComplete
{

    protected static $_loaded_js = array();
    protected static $_loaded_css = array();

    /** 
     * @param array|string $src like /path/to/file.css
     * @return string
     */
   public static function css($src)
   {
       $basePath = Crud_Config::getOption(Crud_Config::OPTION_CSS_BASE_PATH);
       //array
       if (is_array($src)) {
           $ret = '';
           foreach($src as $curElem) {
               $ret .= self::css($curElem);
           }
           return $ret;
           
       } else { //string
           if (strpos($src,'/')!==false) {
               trigger_error('css should not contain a slash', E_USER_WARNING);
           }
           $ret = '';
           if (!in_array($src, self::$_loaded_css)) {
               $ret =  '<link href="' . $basePath . $src . '" rel="stylesheet" type="text/css"/>';
               self::$_loaded_css[] = $src;
           }
           return $ret;
           
       }
   }

   /**
     * @param array|string $src like /path/to/file.js
     * @return string
     */
   public static function js($src)
   {
       $basePath = Crud_Config::getOption(Crud_Config::OPTION_JS_BASE_PATH);

       //array
       if (is_array($src)) {
           $ret = '';
           foreach($src as $curElem) {
               $ret .= self::js($curElem);
           }
           return $ret;

       } else { //string
           if (strpos($src,'/') !== false) {
               trigger_error('css should not contain a slash', E_USER_WARNING);
           }
           $ret = '';
           if (!in_array($src, self::$_loaded_js)) {
               $ret =  '<script type="text/javascript" src="' . $basePath . $src . '"></script>';
               self::$_loaded_js[] = $src;
           }
           return $ret;
       }
       
   }

   /** autocomplete_element.js must contain the "autocompleteFormElement" function
    */
   public static function getHTMLBeforeForm($autoCompleteScript/*='/js/autocomplete_element.js'*/)
   {
        return  self::css(array('autocomplete.css', 'jquery-ui.css')).
                self::js(array('jquery-ui.min.js', 'autocomplete.js', $autoCompleteScript));
   }

   /** 
    * @param array $options ('id'=>'css id',
    *                       'url'=>'json url for autocompletion',
    *                       'ok'=>'message when ok (optional)',
    *                       'err'=>'message when err (optional)
    *                       'searchLabel' => "seach By name"
    *                       'size' => 30
    * ')
    * @return <type>
    */
   public static function getJsAfterForm(array $options) //getHTMLAfterForm
   {
        $ret  = sprintf(   	
             'autocompleteFormElement(%s, %s, ',
             self::jsString($options['id']),
             self::jsString($options['url']));
        $ret .= '{';
        $first = true;
        foreach($options as $k => $v) {
            if($first) $first = false;
            else $ret .= ', ';
            $ret .= $k . ' : ' . (substr($v, 0, 8) == 'function' ? $v : self::jsString($v) );
        }
        $ret .= '});';
        return $ret;
   }

   /** e.g:
    * is not => 'is not'
    * isn't => 'isn\'t'
    */
   private static function jsString($src)
   {
       return sprintf("'%s'", str_replace('\'', '\\\'', $src));
   }

   /** for templates. Prints the data in the format readable by autocomplete element
    * @param array $recordsOrString
    * @param string $id
    * @param string $second
    * @param boolean $displayIdBrackets prints the ID inside brackets after the name
    */
   public static function autoCompleteList($recordsOrString, $id = 'id', $second = 'name', $displayIdBrackets = false)
   {
       $ret = '';
       if (is_array($recordsOrString)) {
       	
           foreach ($recordsOrString as $record) {
               if(is_array($id)) {
               		$first = true;
               		foreach($id as $_id) {
               			if(isset($record[$_id])) {
	               			if($first) {
	               				$first = false;
	               			} else {
	               				$ret .= '-';
	               			}               				
               				$ret .= self::convertForAutoComplete($record[$_id]);
               			}
               			
               		}
               } else {
               		$ret .= self::convertForAutoComplete($record[$id]);
               }
               $ret .='|';
               //var_dump($record);die;
               if(is_array($second)) {
                    $first = true;
               		foreach($second as $_second) {
               			if(isset($record[$_second])) {
	               			if($first) {
	               				$first = false;
	               			} else {
	               				$ret .= '  ►  ';
	               			}               				
               				$ret .= self::convertForAutoComplete($record[$_second]);
               			}
               		}
               } else {
	               $ret .= self::convertForAutoComplete($record[$second]) .
	                    ($displayIdBrackets ? ' (Id = '.intval($record[$id]).')' : '');
               }
               $ret .= PHP_EOL;
           }
       } else { //obsolete ??
           $ret =  $recordsOrString;
       }
       return $ret;
    }
     

    public static function convertForAutoComplete($str)
    {
        $str = substr(
            str_replace(array("\n", '|'), array(" " ," "), $str),
            0,
            100
        );
        return $str;
    }

    /** for controller, search action. it implements the logic to return the records for the tpl
     * <code>
     *   <?php $this->layout()->disableLayout();  ?>
     *   <?php echo Crud_Forms_Helpers_AutoComplete::autoCompleteList($this->records,
     *  'id','name');
     *  </code>
     *
     */
    public static function getRecordsForSearchAction(
        Crud_Model_AutoCompleteInterface $model,
        $request
    )
    {
        $id = $request->getParam('id', 0);
        $q  = $request->getParam('q', null);
       
        $return = null;
        if ($id) {
            if ($q) {
                try {
                    $row    = $model->getByPK($q);
                    $return = $model->getNameAutoComplete($row);
                } catch (Zend_Exception $e){
                    $return = 'not found' ;#.(APPLICATION_ENV=='production' ? '' : $e->getMessage());
                }
            }

        } else {
            $return = array();
            $q = Crud_Helpers_Utils::cleanQuerySearch($q);
             if (strlen($q)>1) {
                $return = $model->search($q, $request->getParam('limit', 20));
            }
        }
        return $return;
    }
   
}