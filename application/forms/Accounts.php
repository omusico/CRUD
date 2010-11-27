<?php

class Application_Form_Accounts extends Crud_Forms_Abstract
{
    //protected $_whiteListElements = array();

    public function init()
    {
        //... additional code ...
        parent::init();
    }


    protected function getModel()
    {
        return new Application_Model_DbTable_Accounts();
    }


    public function getOrderForm($formValues)
    {
        return new Application_Form_Order_Accounts(
            null,
            $this->_model,
            $formValues
        );
    }

    public function getFilterForm($formValues)
    {
        return new Application_Form_Filter_Accounts(
            null,
            $this->_model,
            $formValues
        );
    }

    

    protected function _add_custom_elements()
    {
        

    }

}
