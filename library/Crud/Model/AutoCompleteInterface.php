<?php
/*
 * interface for Model used by CRUD controller
 * Crud_Model_Interface
 *
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
Interface Crud_Model_AutoCompleteInterface extends Crud_Model_CommonInterface{
     
     /**
      * MUST return the string made from the row. It's used to populate edit forms.usually returns $row['name']
      */
     public function getNameAutoComplete($row);
     /*{
        return $row['title'];
      }*/

     /** MUST return an array of the fetchAll with elements "id" and "name" used to make the list
      * See Crud_Model_Abstract for a sample implementation
      */
     public function search($q, $limit);
     

     

}