<?php
class Application_Form_Filter_Bugs extends Crud_Forms_Filter_Abstract {
   //protected $_whitelist = array();
    public function _postProcessElements()
    {
        //common elements
        $modeEqualElem = new Zend_Form_Element_Hidden('mode', array('value'=>'equalto'));

        //bug status
        $elemStatusValue = new Zend_Form_Element_Select('val');
        $elemStatusValue->setLabel('')
                        ->addMultiOptions(
                           Application_Form_Bugs::getStatusList()
                        );
        $this->_subForms['bug_status']->addElement($elemStatusValue);
        $this->_subForms['bug_status']->addElement($modeEqualElem);


        //account dropdowns
        $accountsModel = new Application_Model_DbTable_Accounts();
        $accountList = $accountsModel->getAllArray();
        $elemAccount = new Zend_Form_Element_Select('val');
        $elemAccount->setLabel('')->addMultiOptions($accountList);
        $this->_subForms['reported_by']->addElement($elemAccount);
        $this->_subForms['reported_by']->addElement($modeEqualElem);
        $this->_subForms['assigned_to']->addElement($elemAccount);
        $this->_subForms['assigned_to']->addElement($modeEqualElem);
        $this->_subForms['verified_by']->addElement($elemAccount);
        $this->_subForms['verified_by']->addElement($modeEqualElem);
        







    }


}