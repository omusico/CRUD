<?php

class Application_Form_BugsProducts extends Crud_Forms_Abstract
{
    //protected $_whiteListElements = array();

    public function init()
    {
        //... additional code ...
        parent::init();
    }


    protected function getModel()
    {
        return new Application_Model_DbTable_BugsProducts();
    }


    public function getOrderForm($formValues)
    {
        return new Application_Form_Order_BugsProducts(
            null,
            $this->_model,
            $formValues
        );
    }

    public function getFilterForm($formValues)
    {
        return new Application_Form_Filter_BugsProducts(
            null,
            $this->_model,
            $formValues
        );
    }

    

    protected function _add_custom_elements()
    {
        $groupModel = new Application_Model_DbTable_Bugs();
        $elem = new Zend_Form_Element_Select('bug_id',array('label'=>'Bug'));
        $elem->setMultiOptions($groupModel->getForDropDown());
        $this->_formElements['bug_id'] = $elem;

        $groupModel = new Application_Model_DbTable_Products();
        $elem = new Zend_Form_Element_Select('product_id',array('label'=>'Product'));
        $elem->setMultiOptions($groupModel->getForDropDown());
        $this->_formElements['product_id'] = $elem;
    }

}
