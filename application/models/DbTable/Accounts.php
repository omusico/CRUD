<?php

class Application_Model_DbTable_Accounts extends Crud_Model_DbTable_Abstract
{

    protected $_name = 'accounts';
    protected $_primary = 'account_name';

    /**
     * Get accounts
     * @return array of elements (account_name => fullname)
     */
    public function getAllArray()
    {
        $select = $this->select();
        $all = $this->fetchAll($select)->toArray();
        $ret = array();
        foreach($all as $v) {
            $ret[$v['account_name']] = $v['fullname'];
        }
        return $ret;
    }

} 