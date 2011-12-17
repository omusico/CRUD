<?php

namespace Crud\Forms\Filter;

/**
 * general form for filtering data. Ctor needs the model (to take the metadata)
 * AND post data to populate the form.
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
abstract class AbstractFilter extends \Zend_Form
{
    protected $_model;
    protected $_metadata;
    protected $_postData;
    protected $_whitelist = null; //define = array() to use overWriteOptions
    protected $_blacklist = null;
    protected $_overWriteOptions = null;
    protected $_labels = array();

    protected $_subForms;

    protected $_textSize = 10;

    const EQUAL_TO = 'equalto';
    const GREATER_THAN = 'greaterthan';
    const LESS_THAN = 'lessthan';
    const CONTAINS = 'contains';
    const DIFFERENT_FROM = 'differentfrom';
    const IS_NULL = 'isnull';
    const IS_NOT_NULL = 'isnotnull';

    public static $modes = array(
        self::EQUAL_TO       =>'Equal To',
        self::GREATER_THAN   =>'Greater Than',
        self::CONTAINS       =>'Contains',
        self::LESS_THAN      =>'Less Than',
        self::DIFFERENT_FROM =>'Different From',
        //self::IS_NULL        =>'Is null',
        //self::IS_NOT_NULL    =>'Is not null'
    );

    /**
     * Ctor
     *
     * @param <type> $options
     * @param \Crud\Model\InterfaceModel $model
     * @param <type> $postData
     */
    public function __construct(
        $options, \Crud\Model\InterfaceModel $model, $postData = array()
    )
    {
        $this->_model = $model;
        $this->_metadata = $model->getMetadata();
        $this->_postData = $postData;
        parent::__construct($options);
    }

    /**
     * To override if needed
     *
     */
    protected function _getElementValuesRules()
    {
        return array();
    }

    /**
     * Generates rules for filter form. reading from metadata
     *
     * @return array rules using custom format
     */
    protected function _getElementRules()
    {
        $columns = array_keys($this->_metadata);
        //fetch metadata and make
        foreach ($this->_metadata as $v) {
            $name = $v['COLUMN_NAME'];

            if (
                (
                    //i f whitelist is not defined ...
                    is_null($this->_whitelist)
                     //OR the element is     in the whitelist AND
                    || in_array($name, $this->_whitelist)
                )
                &&
                (
                    // same with blacklist
                    is_null($this->_blacklist)
                    || !in_array($name, $this->_blacklist)
                )
            ) {
                //make default options depending on the metadata
                $options = array();
                if (
                    in_array($v['DATA_TYPE'], array('bigint', 'tinyint', 'int'))
                ) {
                    $options[] = self::EQUAL_TO;
                    $options[] = self::GREATER_THAN;
                    $options[] = self::LESS_THAN;
                    $options[] = self::DIFFERENT_FROM;
                } else if (
                    in_array($v['DATA_TYPE'], array('varchar', 'char', 'text'))
                ) {
                    $options[] = self::CONTAINS;
                    $options[] = self::EQUAL_TO;
                } else if (in_array($v['DATA_TYPE'], array('datetime'))) {
                    $options[] = self::GREATER_THAN;
                    $options[] = self::LESS_THAN;
                } else if (substr($v['DATA_TYPE'], 0, 4)=='enum') {
                    $options[] = self::EQUAL_TO;
                } else { //text ??
                     $options[] = self::EQUAL_TO;
                     $options[] = self::CONTAINS;
                }
                $ret[$name] = $options;
            }
        }

        foreach ((array)$this->_overWriteOptions as $k=>$v) {
            if (!in_array($k, $columns)) {
                trigger_error(
                    'filter: column [' . $k . '] not found in the columns list '
                    . print_r($columns, 1),
                    E_USER_WARNING
                );
            }
            $ret[$k] = $v;
        }

        return $ret;
    }

    /**
     * Get label name for filter
     *
     * @param string $fieldName
     * @return string
     */
    protected function getLabel($fieldName)
    {
        if (isset($this->_labels[$fieldName])) {
            return $this->_labels[$fieldName];
        } else {
            return ucfirst(
                str_replace(array('_'), array(' '), $fieldName)
            );
        }
    }

    /**
     * Init
     */
    public function init()
    {
        //form attrib
        $this->setMethod('post');
        $this->setAttrib('class', '');
        $this->setAction('#');
        $this->setAttrib('class', 'subforms form_filter');

        //apply & reset
        $this->addElement(
            new \Zend_Form_Element_Submit(
                'bt_submit', 'Apply selected filter(s)'
            )
        );
        $this->addElement(
            new \Zend_Form_Element_Reset('bt_resetall', 'Reset all filters')
        );

        $element = new \Zend_Form_Element_Hidden('form_filter_submitted');
            $element->setValue(1);
            $this->addElement($element);
        
            
        //get rules (other methods)
        $rules      = $this->_getElementRules();
        $ruleValues = $this->_getElementValuesRules();

        //set rules
        foreach ((array)$rules as $name => $options) {
            
            $this->_subForms[$name] = new \Zend_Form_SubForm();
            $element = new \Zend_Form_Element_Checkbox('enabled');
                $element->setLabel($this->getLabel($name));
                $this->_subForms[$name]->addElement($element);

            $element = new \Zend_Form_Element_Select('mode');
                $element->setLabel('');
                if (empty($options)) {
                    foreach (self::$modes as $k=>$v) {
                        $element->addMultiOption($k, $v);
                    }
                } else {
                    foreach ((array)$options as $v) {
                        $element->addMultiOption($v, self::$modes[$v]);
                    }
                }
                $element->setAttrib('size', count($element->getMultiOptions()));
                if (count($element->getMultiOptions())==1) {
                    $element->setAttrib('multiple', 'multiple');
                }
                $this->_subForms[$name]->addElement($element);
            //set value field depending on the field type
            //warning => field mispelling (better to show)
            $dataType = $this->_metadata[$name]['DATA_TYPE'];
            if (substr($dataType, 0, 4)=='enum') {
                    $element = new \Zend_Form_Element_Select('val');
                    //parse enum('y', 'n')=> array(y, n)
                     preg_match_all("#\'([^']+)\'#", $dataType, $values);
                     foreach ($values[1] as $v) {
                        $element->addMultiOption($v, ' ' . $v . ' ');
                     }
                     $element->setAttrib(
                         'size',
                         count($element->getMultiOptions())
                     );
            } else {
                $element = new \Zend_Form_Element_Text('val');
                $element->setAttrib('size', $this->_textSize);
                if ($this->_metadata[$name]['DATA_TYPE']=='datetime') {
                    $defaultVal = date('Y-m-d H:i:s');
                    $element->setValue($defaultVal);
                    $element->setAttrib('size', strlen($defaultVal)+5);
                }
            }
            $this->_subForms[$name]->addElement($element);
            
        }

        $this->_postProcessElements();

        //add subforms
        $this->addSubForms($this->_subForms);

        //apply & reset
        $this->addElement(
            new \Zend_Form_Element_Submit(
                'bt_submit2', 'Apply selected filter(s)'
            )
        );
        $this->addElement(
            new \Zend_Form_Element_Button('bt_resetall2', 'Reset all filters')
        );

        /*$element = new \Zend_Form_Element_Reset('reset');
            $element->setLabel('Reset filters');
            $this->addElement($element);*/

        //set decorators
        /*foreach (array_merge($this->_subForms, array($this)) as $form) {
            $form->setElementDecorators(array(//$decorators
                'viewHelper',
                'Errors',
                array('Description', array('tag' => 'span',
                   'class' => 'description')),
                array(
                    array('data' => 'HtmlTag'),
                    array('tag' => 'span') //td
                ),
                array('Label', array('tag' => 'span')), //td
                array(
                    array('row'=>'HtmlTag'),
                    array('tag'=>'span') //tr
                )
            ));
            
        }

        $this->setDecorators(array(
                'FormElements',
                array(
                    array('data' => 'HtmlTag'),
                    array('tag' => 'span') //table
                    ),
                'Form'
                )
             );*/

        //populate
        if (isset($this->_postData['form_filter_submitted'])) {
            $this->populate($this->_postData);
        }
    }

    /*public function populate(array $values)
    {
        pd($this->_elements);
        foreach ($values as $k => $v) {
            $this->_elements[$k]->setLabel('SELECTEDDDD');
        }

        //foreach ()

        return self::populate($values);
    }*/

    /**
     * Process existing elements
     */
    public function _postProcessElements()
    {
        //to implement when needed
    }

    /**
     * Add extra JQuery scripts on fieldset
     *
     */
    public function render(\Zend_View_Interface $view = null)
    {
        $addText = '<script type="text/javascript">
            /* add class "active" to fieldset with a checked checkbox */
            function updateFormFilterCheckBoxes()
            {
              $("form.form_filter input:checkbox").each(function() {
                 if ($(this).is(":checked")) {
                    $(this).parent().parent().parent().addClass("active");
                 } else {
                    $(this).parent().parent().parent().removeClass("active");
                 }
              });
            }

            function makeFilterApplyBig() {
                $("#bt_submit").addClass("buttonToClick");
                $("#bt_submit2").addClass("buttonToClick");
            }

            /* event listeners */
            $(document).ready(function() {
                /* initial background of form populated */
                updateFormFilterCheckBoxes();

                /* update background at every tuck/untick */
                $("form.form_filter input:checkbox").change(function() {
                     updateFormFilterCheckBoxes();
                     makeFilterApplyBig();
                });
                
                /* tick the select box and update background when clicking
                 * on input and select fields */
                var fieldChangeListener = function () {
                     $(this).parent().parent().parent().find("input:checkbox")'
                     .'.attr("checked", "checked");
                     updateFormFilterCheckBoxes();
                     makeFilterApplyBig();
                }
                $("form.form_filter select").change(fieldChangeListener);
                $("form.form_filter input:text").keydown(fieldChangeListener);

                /*resetall button event*/
                $("#bt_resetall").click(function() {
                    $("form.form_filter input:checkbox").attr("checked", null);
                    updateFormFilterCheckBoxes();
                    this.form.submit();
                });
                
            });
            </script>';
        return $addText . parent::render($view);
    }

    
}