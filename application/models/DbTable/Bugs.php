<?php

class Application_Model_DbTable_Bugs extends Crud_Model_DbTable_Abstract
{

    protected $_name = 'bugs';
    protected $_primary = 'bug_id';

    public function _getSelectForPaginator()
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('b'=>'bugs'))
            ->joinLeft(
               array('a1'=>'accounts'),
               'a1.account_name = b.reported_by',
               array('reported_by_fullname' => 'fullname')
            )->joinLeft(
               array('a2'=>'accounts'),
               'a2.account_name = b.assigned_to',
               array('assigned_to_fullname' => 'fullname')
            )->joinLeft(
               array('a3'=>'accounts'),
               'a3.account_name = b.verified_by',
               array('verified_by_fullname' => 'fullname')
            );
        return $select;
    }

    public function getForDropDown()
    {
        $ret = array(0=>'-- select one --');
        $records = $this->fetchAll();
        foreach($records as $record) {
            $ret[$record->bug_id] = $record->bug_description;
        }
        return $ret;
    }


} 