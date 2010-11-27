<?php
/**
 * Class Name
 *
 * @category  Crud class
 * @package   Crud
 * @author    elvis ciotti <elvis@phpntips.com>
 * @copyright 2010 Phpntips.com
 * @license   http://framework.zend.com/license/new-bsd  New BSD License
 * @version   Release: 1.0
 * @link      http://www.phpntips.com/crud
 */
class Crud_Forms_Order_Helper  { //Crud_Forms_Order_Helper

   
   public static function getOrderQuery(Zend_Controller_Request_Http $request)
   {
       //id desc
       if (!$request->getParam('order', false)) {
           return null;
       }
       return $request->getParam('order') . ' ' . $request->getParam('direction','desc');
   }

   public static function isFormPosted(Zend_Controller_Request_Http $request){
        return $request->isPost() && $request->getPost('form_order_submitted',0);
   }

   public static function getPostOrder(Zend_Controller_Request_Http $request){
        return $request->getPost('order', null); //TODO to test
   }

   public static function getPostDirection(Zend_Controller_Request_Http $request){
        return $request->getPost('direction', 'asc');
   }

   public static function url(Zend_View $view, Zend_Controller_Request_Http $request)
   {
        return $view->url(
            array(
                'action'   => 'index',
                'page'     => 1, //reset page when changing order
                Crud_Forms_Order_Abstract::ORDER_PARAM_NAME    => self::getPostOrder($request),//   ,
                Crud_Forms_Order_Abstract::ORDER_DIRECTION_NAME => self::getPostDirection($request)//
            )
         );

    }


   
}