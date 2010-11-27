<?php

class Admin_AccountsController extends Crud_Controller_Abstract
{

    protected $_useInternalListView = true;
    protected $_recordsPerPage = 3;

    protected function _getCrudModel()
    {
        //return new Application_Model_DbTable_Categories();

        //pd(Zend_Loader_Autoloader::getInstance());


        return new Application_Model_DbTable_Accounts();
    }

    protected function _getCrudForm()
    {
        return new Application_Form_Accounts();
    }

    public function init()
    {
        /* Initialize action controller here */
    }    

}