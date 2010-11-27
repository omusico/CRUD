<?php

class Admin_BugsController extends Crud_Controller_Abstract
{

    protected $_useInternalListView = true;

    protected function _getCrudModel()
    {
        //return new Application_Model_DbTable_Categories();

        //pd(Zend_Loader_Autoloader::getInstance());


        return new Application_Model_DbTable_Bugs();
    }

    protected function _getCrudForm()
    {
        return new Application_Form_Bugs();
    }

    public function init()
    {
        /* Initialize action controller here */
    }    

}