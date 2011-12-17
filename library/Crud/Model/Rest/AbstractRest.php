<?php

namespace Crud\Model\Rest;

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

abstract class AbstractRest implements \Crud\Model\InterfaceModel
{

    
    protected $_metadata = null;

     
    public function getMetadata()
    {
    }

    public function getByPK($id)
    {
    }

    public function fetchPaginatorAdapter(
        $filters = array(), $sortField = null, array $records = array()
    )
    {
        
    }
     
    public function fetchAll(
        $where = null, $order = null, $count = null, $offset = null
    )
    {
        
    }    
    
    public function insert(array $data)
    {
        
    }

    public function delete($id)
    {
        
    }

    public function update(array $data, $id)
    {
        
    }

}