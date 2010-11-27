<?php

class Admin_BugsProductsController extends Crud_Controller_Abstract
{

    protected $_useInternalListView = true;


    public function preDispatch()
    {
        $this->view->iframeTitle = 'Bugs - Products relationships';

        //set iframe layout
        if ($this->_request->getParam('iframe', 0)) {
            $layout = Zend_Layout::getMvcInstance();
            $layout->setLayout('iframe');
        }
        //set columns to hide (index script view)
        $params = Crud_Forms_Filter_Helper::stringToArray(
            $this->_request->getParam('filter', '')
        );
        $this->view->hideProduct = !empty($params['product_id']['val']);
        $this->view->hideBug = !empty($params['bug_id']['val']);


        parent::preDispatch();
    }


    public function getFormOptions()
    {
        $params = Crud_Forms_Filter_Helper::stringToArray(
            $this->_request->getParam('filter', '')
        );

        if (!empty($params['product_id']['val'])) {
            return array(
                'fixed_values' => array(
                    'product_id'=>$params['product_id']['val']
                )
            );
        }

        if (!empty($params['bug_id']['val'])) {
            return array(
                'fixed_values' => array(
                    'bug_id'=>$params['bug_id']['val']
                )
            );
        }
    }


    protected function _getCrudModel()
    {
        return new Application_Model_DbTable_BugsProducts();
    }

    protected function _getCrudForm()
    {
        return new Application_Form_BugsProducts($this->getFormOptions());
    }

    public function init()
    {
        /* Initialize action controller here */
    }

    protected function _postIndex()
    {

        foreach ($this->view->data as &$row) {
            $row['_bug_description']= $row['bug_description'] . sprintf('(Id=%d)', $row['bug_id']);
            $row['_product_name']   = $row['product_name'] . sprintf('(Id=%d)', $row['product_id']);

        }
    }



}