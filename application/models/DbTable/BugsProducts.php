<?php

class Application_Model_DbTable_BugsProducts extends Crud_Model_DbTable_Abstract
{

    protected $_name = 'bugs_products';
    protected $_primary = array('bug_id', 'product_id');

    protected $_sqlAlias = 'bp';

    public function _getSelectForPaginator()
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('bp'=>'bugs_products'))
            ->joinLeft(
               array('b'=>'bugs'),
               'b.bug_id = bp.bug_id',
               array('bug_description' => 'bug_description')
            )->joinLeft(
               array('p'=>'products'),
               'p.product_id = bp.product_id',
               array('product_name' => 'product_name')
            );
        return $select;
    }


    public function getNumberOfBugsOfProduct($productId)
    {
        //TODO  improve
        $select = $this->select()
                ->from($this->_name, array('c'=>'COUNT(bug_id)'))
                ->where('product_id=?', $productId);

        $row = $this->fetchRow( $select );
        return $row['c'];

    }

} 