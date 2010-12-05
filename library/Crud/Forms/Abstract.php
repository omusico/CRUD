<?php
/**
 * Abstract CRUD form class
 * Reads table metadata and add automatically components
 *
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
abstract class Crud_Forms_Abstract extends Zend_Form
    implements Crud_Forms_Interface
{
    
    protected  $_checkTableStructure = true;
    /* contains name of elements to display */
    protected  $_whiteListElements = null;
    /* contains name of elements to hide */
    protected  $_blackListElements = null;
    /* Set fields with default values to hide in forms and use when inserting
     * e.g: array('type'=>6, 'is_visible'=>1)
     */
    protected  $_fixedValues = array();
    /*
     * MAY BE DEFINED (it contains the metadata (Zend_Db_Table style)),
     * otherwise automatically taken from model source
     * Array
        ( [id] => Array
                (
                    [DATA_TYPE] => int
                    [NULLABLE] =>
                )
           [make] => Array
              (
                    [DATA_TYPE] => string
                    [NULLABLE] => 1
               )
     */
    protected  $_metadata = array();

    //fields necessary for logic
    protected  $_currentRow = array();
    protected  $_formElements;
    protected  $_columns; //columns ls

    //additional html when rendering
    protected  $_preAdditionalHTML  = '';
    protected  $_postAdditionalHtml = '';
    protected  $_postAdditionalJsCode = '';
    /*
     * Fields used as INT but treated like TIMESTAMP YY-mm-dd h:i using js
     */
    protected  $_fieldsTimestamps = array();

    /**
     * @var string the default date format output
     */
    const Application_DATE_FORMAT = "Y-m-d H:i:s";
    const Application_TIMESTAMP_FORMAT = "Y-m-d H:i:s";

    const KEY_DATA_TYPE =       'DATA_TYPE';
    const KEY_NULLABLE =        'NULLABLE';
    const KEY_LENGTH =          'LENGTH';
    const KEY_PRIMARY =         'PRIMARY';
    const KEY_AUTO_INCREMENT  = 'IDENTITY';

    const TYPE_INT = 'int';
    const TYPE_VARCHAR = 'varchar';

    protected $_model = null; //change to protected ?
    protected $_submitOrder = 100;
    
    /**
     * ctor
     *
     * @param array $options Zend_Form classical options,
     *        'fixed_values'=>array(...)
     * @param array $metadata
     */
    public function __construct($config = array())
    {
        //call getModel and set internal field
        $this->_model = $this->getModel();
        if (!($this->_model instanceof Crud_Model_Interface)) {
            throw new Zend_Exception(
                'model not an instance of Crud_Model_Interface'
                . print_r($this->_model, 1)
            );
        }
        //set metadata asking the models
        $this->_metadata = $this->getMedatadaFromTheModel();
        if ($this->_checkTableStructure) {
            $this->_checkTableStructure();
        }

        //set option
        if (is_array($config) && array_key_exists('fixed_values', $config)) {
            $this->_fixedValues = $config['fixed_values'];
        }

        parent::__construct($config);
    }

    /**
     * init
     */
    public function init()
    {
        $this->setName('form_name');
        $this->setAttrib('class', 'crud');
        // add all the elements
        $this->_generateElements();
        $this->_add_custom_elements();
        $this->_removeFixedElements();
        $this->addElements($this->_formElements);

        // decorator
        /*if ($this->_decorator_mode=='table') {
            $this->setElementDecorators(array(//$decorators
                    'viewHelper',
                    'Errors',
                    array('Description',
                    array('tag' => 'span', 'class' => 'description')),
                    array(
                        array('data' => 'HtmlTag'),
                        array('tag' => 'td')
                    ),
                    array('Label', array('tag' => 'td')),
                    array(
                        array('row'=>'HtmlTag'),
                        array('tag'=>'tr')
                    )
                ));

            $this->setDecorators(array(
                        'FormElements',
                        array(
                            array('data' => 'HtmlTag'),
                            array('tag' => 'table')
                            ),
                        'Form'
                        )
                );
        }*/
    }

    /**
     * populate form
     * @param array $values
     */
    public function populate(array $values)
    {
        //[..]
        parent::populate($values);
    }

    /**
     * getFixedValues
     * @return array
     */
    public function getFixedValues()
    {
        return $this->_fixedValues;
    }

    /**
     * get model, to implement
     */
    abstract protected function getModel();

    
    /**
     * return the getMetadata() of the model, instantiated inside this method
     * 
     * @returns array
     */
    public function getMedatadaFromTheModel()
    {
        return $this->_model->getMetadata();
    }

    /**
     * return order form
     * @param array $postData
     * @return Zend_Form|null
     */
    public function getOrderForm($postData)
    {
        return null;//new Zend_Form()
    }

    /**
     * return filter form
     * @param array $postData
     * @return Zend_Form|null
     */
    public function getFilterForm($postData)
    {
        return null;//new Zend_Form()
    }

    /**
     * render, add extra js code defined in fields
     *
     * @param Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    { 
        return $this->_preAdditionalHTML . parent::render($view)
               . '<script type="text/javascript">'
               . $this->_postAdditionalJsCode
               . '</script>' . $this->_postAdditionalHtml;
    }

    /**
     * Check PK, and others [...]
     */
    protected function _checkTableStructure()
    {
        $errs =  array();
        $columns = array_keys($this->_metadata);
        
        if (!$this->_model->info(Zend_Db_Table_Abstract::PRIMARY)) {
            $errs[] = ': PRIMARY KEY not defined !';
        }
        /*if (! isset($this->_metadata['id'])) {
            $errs[] = ': column [id] not found !';
        }

        if (!isset($this->_metadata['id']['PRIMARY'])
          || !$this->_metadata['id']['PRIMARY']) {
            $errs[] = ': column [id] is not primary';
        }

        if (!isset($this->_metadata['id']['IDENTITY'])
          || !$this->_metadata['id']['IDENTITY']) {
            $errs[] = ': column [id] is not auto_increment !';
        }*/

        foreach ($errs as $err) {
            trigger_error(get_class($this) . $err, E_USER_WARNING);
        }
    }

    /**
     * to implement to customize/add form elements
     */
    protected function _add_custom_elements()
    {
       
        /*
         *   $this->_formElements[''] = ..
         */
    }

    /**
     * remove element with fixed values (as not editable)
     */
    protected function _removeFixedElements()
    {
        foreach ($this->_fixedValues as $k => $v) {
            if (isset($this->_formElements[$k])) {
                unset($this->_formElements[$k]);
            }
        }
    }

    
    /**
     * check if metadata option is true
     *
     * @param string $key
     * @param string $defVal
     * @return bool
     */
    protected function _isOn($key, $defVal = null)
    {
        return isset($this->_currentRow[$key])
               ? $this->_currentRow[$key] : $defVal;
    }    

    /**
     * fetches the metadata and creates the form + sumbit element
     *
     */
    protected function _generateElements()
    {
        $this->_formElements = array();

        //if whitelist is defined, reorder metdata using whitelist order
        if ($this->_whiteListElements) {
            //order  $this->_metadata using order in $this->_whiteListElements
            $metadataNew = array();
            foreach ($this->_whiteListElements as $wle) {
                if (isset($this->_metadata[$wle])) {
                    $metadataNew[$wle] = $this->_metadata[$wle];
                } else {
                    trigger_error(
                        get_class()
                        . ": whitelist element '$wle' not found in the db"
                    );
                }
            }
            $this->_metadata = $metadataNew;
        }

        foreach ($this->_metadata as $field => $this->_currentRow) {
            //if ($field=='description') pd($this->_currentRow);
            if (
                // allowed whitelist if whitelist not defined in it
                (
                    !$this->_whiteListElements
                    || in_array($field, $this->_whiteListElements)
                ) &&
                // allowed blacklist if blacklist not defined
                // or NOT in teh blacklist
                (
                    !$this->_blackListElements
                    || !in_array($field, $this->_blackListElements)
                 )
            ) {
                //hidden fields for not single primary keys auto-increment
                if ($this->_isOn('PRIMARY')
                     &&
                    count(
                        $this->_model->info(Zend_Db_Table_Abstract::PRIMARY)
                    ) === 1 //not compound
                    &&
                    $this->_isOn('IDENTITY') //auto-increment
                ) {
                    $element = new Zend_Form_Element_Hidden($field);
                    $element->setLabel('');

                // fields
                } else {
                    
                    /*if ($field === 'password2') { //TO REVISE
                        $element = new Zend_Form_Element_Password($field);
                    } else */
                    if (
                        in_array(
                            $this->_currentRow['DATA_TYPE'],
                            array('text', 'mediumtext', 'longtext')
                        )
                    ) {
                        $element = new Zend_Form_Element_Textarea($field);
                        $element->setAttrib('rows', 3)->setAttrib('cols', 40);
                    } else {
                        $element = new Zend_Form_Element_Text($field);
                        $element->setAttrib('size', 40);
                    }

                    //label
                    $label = str_replace('_', ' ', ucfirst($field));
                    if ($this->_isOn('NULLABLE')) {
                        $element->setLabel($label);
                        $element->setRequired(false);
                    } else {
                        $element->setLabel($label.' *');
                        $element->setRequired(true);
                    }

                    //timestamp/date/datetime
                    $timeFormats = array(
                        'timestamp'=>'Y-m-d H:i:s',
                        'date'=>'Y-m-d',
                        'datetime'=>'Y-m-d H:i:s'
                    );
                    $timeFormat =
                        isset($timeFormats[$this->_isOn('DATA_TYPE')])
                        ? $timeFormats[$this->_isOn('DATA_TYPE')] : null;
                    if ($timeFormat) {
                        if ($this->_isOn('DEFAULT') == 'CURRENT_TIMESTAMP') {
                            $element->setValue(date($timeFormat, time()));
                        }
                        /*$this->_postAdditionalJsCode .=
                                Crud_Forms_Helpers_DatePicker::getJs($field);*/
                    }

                    //length validator
                    if ($this->_isOn('LENGTH')) {
                        $element->addValidator(
                            new Zend_Validate_StringLength(
                                array('max' => $this->_isOn('LENGTH'))
                            )
                        );
                        $element->setAttrib(
                            'MAX_LENGTH', $this->_isOn('LENGTH')
                        );
                    }
                }

                // int validator (also for PK)
                if ($this->_isOn('DATA_TYPE')==='int') {
                    $element->addValidator(new Zend_Validate_Int());
                }

                $this->_formElements[$field] = $element;
            } //end if
        }

        // submit
        $this->_formElements['submit'] = new Zend_Form_Element_Submit('submit');
        $this->_formElements['submit']->setAttrib('id', 'submitbutton')
            ->setOrder($this->_submitOrder)
            ->setLabel('Save');

    }

    
    /**
     * Returns the columns of the database related table
     * @return array
     */
    public function getColumns()
    {
        if ($this->_columns) {
            return $this->_columns;
        } else {
            //temp solution, better to use _metadata
            //and instersect with form_elements
            $formElem = $this->_formElements;
            unset($formElem['submit']);
            return array_keys($formElem);
        }
    }

    /**
     * return metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * Return the code for the ajax search,
     * to append to  $this->_postAdditionalJsCode
     *
     * @param <type> $options
     * @return <type>
     */
    protected function addAjaxJs($options)
    {
        if (isset($this->_fixedValues[$options['id']])) {
           return '';
        } else {
            return  Crud_Forms_Helpers_AutoComplete::getJsAfterForm(
                $options
            );
        }
    }

    /**
     * set the values that will be added
     * into $_fixedValues property of the form
     *
     */
    protected function setFormFixedValues(array $val)
    {
        $this->_formFixedValues = $val;
    }

    
}