<?php

namespace Crud\Forms\Order;
/**
 * Abstract Oder form
 *//**
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
abstract class AbstractOrder extends \Zend_Form
{
    protected $_metadata;
    protected $_queryData;
    protected $_columnNameChecks = false;

    const ORDER_PARAM_NAME      = 'order';
    const ORDER_DIRECTION_NAME  = 'direction';

    /**
     * Ctor
     *
     * @param array $options
     * @param \Crud\Model\InterfaceModel $model
     * @param \Zend_Controller_Request_Http $formValues
     */
    public function __construct(
        $options,
        \Crud\Model\InterfaceModel $model,
        \Zend_Controller_Request_Http $formValues
    )
    {
        $this->_metadata = $model->getMetadata();

        $this->_queryData = array(
            self::ORDER_PARAM_NAME     => $formValues->getParam(
                self::ORDER_PARAM_NAME, null
            ),
            self::ORDER_DIRECTION_NAME => $formValues->getParam(
                self::ORDER_DIRECTION_NAME, null
            ),
            'form_order_submitted' => 1,
            'submit'               => 'Order'
        );

        parent::__construct($options);
    }

    /**
     * Convert underscores to spaces
     *
     * @param string $name
     * @return string
     */
    protected static function humanReadableColumn($name)
    {
        return ucwords(str_replace('_', ' ', $name));
    }

    /**
     * return array of values read from metadata, for dropdown box
     *
     * @return array
     */
    protected function getDropDownValues()
    {
        $valuesFromArguments = $this->getAttrib('orderDropDownMap');
        if (isset($valuesFromArguments) && is_array($valuesFromArguments)) {
            return $valuesFromArguments;
        } else {
            $ret = array();
            foreach ($this->_metadata as $column) {
                $ret[$column['COLUMN_NAME']] =
                    self::humanReadableColumn($column['COLUMN_NAME']);
            }
            return $ret;
        }
    }

    /**
     * init
     */
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('class', 'subforms form_order');
        $this->setAction('#');

        $element = new \Zend_Form_Element_Hidden('form_order_submitted');
            $element->setValue(1);
            $this->addElement($element);
        
        $element = new \Zend_Form_Element_Select(self::ORDER_PARAM_NAME);
           $element->setLabel('Order By');
            $dropDownValues = $this->getDropDownValues();
            $columns = array_keys($this->_metadata);
            foreach ($dropDownValues as $k => $v) {
                $key = is_int($k) ? $v : $k;
                if (!$this->_columnNameChecks || in_array($key, $columns)) {
                    $element->addMultiOption($key, $v);
                } else {
                    trigger_error(
                        'order: column [' . $key . '] not found in the '
                        . 'columns list ' . print_r($columns, 1),
                        E_USER_WARNING
                    );
                }
                
            }
            $this->addElement($element);

         $element = new \Zend_Form_Element_Select(self::ORDER_DIRECTION_NAME);
            $element->addMultiOption('asc', 'Ascending');
            $element->addMultiOption('desc', 'Descending');
            $this->addElement($element);

        $element = new \Zend_Form_Element_Submit('bt_order_submit');
            $element->setLabel('Order');
            $this->addElement($element);

        //set decorator
        $this->setElementDecorators(
            array(//$decorators
                'viewHelper',
                'Errors',
                array(
                    'Description',
                    array('tag' => 'span', 'class' => 'description')
                ),
                array(
                    array('data' => 'HtmlTag'),
                    array('tag' => 'span') //td
                ),
                array('Label', array('tag' => 'span')), //td
                array(
                    array('row'=>'HtmlTag'),
                    array('tag'=>'span') //tr
                )
            )
        );

        $this->setDecorators(
            array(
                'FormElements',
                array(
                    array('data' => 'HtmlTag'),
                    array('tag' => 'span') //table
                    ),
                'Form'
            )
        );
        if (
            $this->_queryData[self::ORDER_PARAM_NAME]
            && $this->_queryData[self::ORDER_DIRECTION_NAME]
        ) {
            $this->populate($this->_queryData);
        }

    }

    /**
     * render: add extra script (class when changing order)
     *
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null)
    {
        $addText = '<script type="text/javascript">
            $(document).ready(function() {
                $("form.form_order select").change(function() {
                    $("#bt_order_submit").addClass("buttonToClick");
                });
            });
            </script>';
        return $addText . parent::render($view);
    }
    
}