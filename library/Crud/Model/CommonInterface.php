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
Interface Crud_Model_CommonInterface {
     
     /**
      * the same of the other interface
      */
     public function getByPK($id);


}