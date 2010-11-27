<?php

class Application_Form_Bugs extends Crud_Forms_Abstract
{
    //protected $_whiteListElements = array();

    public static function getStatusList()
    {
        return array(
            'open'    => 'open',
            'fixed'   => 'fixed',
            'wontfix' => 'wontfix',
            'reopen'  => 'reopen',
        );
    }

    public function init()
    {
        //... additional code ...
        parent::init();
    }



    protected function getModel()
    {
        return new Application_Model_DbTable_Bugs();
    }


    public function getOrderForm($formValues)
    {
        return new Application_Form_Order_Bugs(
            null,
            $this->_model,
            $formValues
        );
    }

    public function getFilterForm($formValues)
    {
        return new Application_Form_Filter_Bugs(
            null,
            $this->_model,
            $formValues
        );
    }

    

    protected function _add_custom_elements()
    {
         $accountsModel = new Application_Model_DbTable_Accounts();
         $allAccounts = $accountsModel->getAllArray();


         if (isset($this->_formElements['reported_by'])) {
            $this->_formElements['reported_by'] =
                new Zend_Form_Element_Select('reported_by');
            $this->_formElements['reported_by']->setLabel('Reported By')
            	 ->addMultiOptions( $allAccounts );
         }

         if (isset($this->_formElements['assigned_to'])) {
            $this->_formElements['assigned_to'] =
                new Zend_Form_Element_Select('assigned_to');
            $this->_formElements['assigned_to']->setLabel('Assigned To')
            	 ->addMultiOptions( $allAccounts );
         }

         if (isset($this->_formElements['verified_by'])) {
            $this->_formElements['verified_by'] =
                new Zend_Form_Element_Select('verified_by');
            $this->_formElements['verified_by']->setLabel('Verified By')
            	 ->addMultiOptions( $allAccounts );
         }


         if (isset($this->_formElements['bug_status'])) {
            $this->_formElements['bug_status'] =
                new Zend_Form_Element_Select('bug_status');
            $this->_formElements['bug_status']->setLabel('Bug Status')
            	 ->addMultiOptions(
                    self::getStatusList()
                 );
         }

    }


    


}
