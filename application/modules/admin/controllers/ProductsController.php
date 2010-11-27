<?php

class Admin_ProductsController extends Crud_Controller_Abstract
{

    protected $_useInternalListView = false;
    protected $_recordsPerPage = 3;

    protected function _getCrudModel()
    {
        return new Application_Model_DbTable_Products();
    }

    protected function _getCrudForm()
    {
        return new Application_Form_Products();
    }

    public function init()
    {
        /* Initialize action controller here */
    }


    protected function _postIndex()
    {

        $bugsProdsModel = new Application_Model_DbTable_BugsProducts();
        foreach ($this->view->data as &$row) {
            $row['_product_name'] = sprintf(
                '%s (Id=%d)',
                $row['product_name'],
                $row['product_id']
            );

            $row['_bugs'] = 
               $bugsProdsModel->getNumberOfBugsOfProduct($row[$this->_modelPK]) . ' bugs<br/>'
               .$this->view->linkIframe(
                    '/admin/bugs-products/index/page/1/iframe/1/filter/product_id-mode%3Dequalto%26product_id-val%3D'.$row[$this->_modelPK],
                    '<img border="0" src="/images/admin_layout/edit.gif">MANAGE',
                    array('pk'=>$row[$this->_modelPK], 'frame_id'=>'bugs')
               );


        }
    }


    #'_bug_description'=>'Bug description',
    #'_product_name'   =>'Product name',


}