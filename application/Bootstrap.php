<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Add autoloader for instances
     * @return <type>
     */
    public function _initLoader()
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
        return $crudAutoloader;
    }
}

