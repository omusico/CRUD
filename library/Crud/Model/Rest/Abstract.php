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

abstract class Crud_Model_Rest_Abstract implements Crud_Model_Interface {

    // module name. to define
    protected $_addURL = 'admin/datasource/add/module_name/coupons';
    protected $_updateURL = 'admin/datasource/update/module_name/coupons';
    protected $_deleteURL = 'admin/datasource/delete/module_name/coupons';
    protected $_describeURL = 'admin/datasource/describe/module_name/coupons';
    protected $_findURL = 'admin/datasource/find/module_name/coupons';
    protected $_insertURL = 'admin/datasource/insert/module_name/coupons';
      
    
    protected $_metadata = null;

     
    public function getMetadata()
    {
    }

    public function getByPK($id)
    {
    }

    public function fetchPaginatorAdapter($filters = array(), $sortField = null, array $records = array() )
    {
    }
     
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
    }    
    
    public function insert(array $data)
    {
    }

    public function delete($id)
    {
    }

    public function update(array $data, $id){
    }

}