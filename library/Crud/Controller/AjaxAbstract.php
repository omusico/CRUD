<?php
/**
 * abstract ajax controller
 *
 * @category  Crud class
 * @package   Crud
 * @author    elvis ciotti <elvis@phpntips.com>
 * @copyright 2010 Phpntips.com
 * @license   http://framework.zend.com/license/new-bsd  New BSD License
 * @version   Release: 1.0
 * @link      http://www.phpntips.com/crud
 */
abstract class Crud_Controller_AjaxAbstract
    extends Zend_Controller_Action
{
    protected $_setHeaderPlainText = true;

    /*
     * Disables layout, view and set plain text
     */
    public function postDispatch()
    {
        //disable layout and view
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')
            ->setNoRender(true);
        $layout = Zend_Layout::getMvcInstance();
        if ($layout instanceof Zend_Layout) {
            $layout->disableLayout(); //noLayout
        }
        
        if ($this->_setHeaderPlainText) {
            $this->_response->setHeader('Content-Type', 'plain/text');
        }
    }
    
}