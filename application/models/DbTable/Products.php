<?php

class Application_Model_DbTable_Products extends Crud_Model_DbTable_Abstract
{

    protected $_name = 'products';
    protected $_primary = 'product_id';

    public function getForDropDown()
    {
        $ret = array(0=>'-- select one --');
        $records = $this->fetchAll();
        foreach($records as $record) {
            $ret[$record->product_id] = $record->product_name;
        }
        return $ret;
    }

}