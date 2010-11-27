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
Interface Crud_Model_Interface  extends Crud_Model_CommonInterface {
     
     public function getMetadata();

     public function getPKName();

     public function fetchPaginatorAdapter($filters = array(), $sortField = null, array $records = array());

     /**
      * same as the one in Zend_Db_Table_Abstract
      */
     public function fetchAll($where = null, $order = null, $count = null, $offset = null);

     /**
      * same as the one in Zend_Db_Table_Abstract
      */
     public function insert(array $data);

     /**
      * same as the one in Zend_Db_Table_Abstract
      */
     public function delete($id);

}