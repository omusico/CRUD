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
class Crudloader extends Zend_Application_Resource_ResourceAbstract
{
  public function init ()
  {
    $options = $this->getOptions();
    if (!empty($options['enabled'])) {
        $this->_setCrudAutoloader();
    }
  }

  protected function _setCrudAutoloader()
  {
     $crudAutoloader = new Zend_Application_Module_Autoloader(
         array(
             'namespace' => 'Application_',
             'basePath'  => dirname(__FILE__),
         )
     );
     $crudAutoloader->addResourceTypes(
         array(
             'form_order'    => array(
                 'namespace' => 'Form_Order',
                 'path'      => 'forms/order',
             ),
             'form_filter'    => array(
                 'namespace' => 'Form_Filter',
                 'path'      => 'forms/filter',
             ),
         )
     );
  }

}