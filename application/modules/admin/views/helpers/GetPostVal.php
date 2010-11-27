<?php

class Zend_View_Helper_GetPostVal extends Zend_View_Helper_Abstract
{
    public function getPostVal($val)
    {
        $post = Zend_Controller_Front::getInstance()->getRequest()->getPost(); //$_POST;
        return isset($post[$val]) ? $post[$val] : '';
    }
}