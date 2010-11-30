<?php

/*
 * CONTROLLER WITH DEFAULT CRUD ACTION IMPLEMENTED
 *
 * 2 methods have to be implemented
 * _getModel() : must return the associated Model  that must implement
 *               Crud_Model_Interface or (easier with Db_Table) extends Crud_Model_Abstract
 * _getForm() must return a form that must extend Crud_Forms_Abstract
 *
 */
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
abstract class Crud_Controller_Abstract extends Zend_Controller_Action 
{
    protected $_recordsPerPage = 25;
    protected $_useInternalListView = false;

    protected $_CrudModel = null;
    protected $_modelPK = null;
    abstract protected function _getCrudModel();

    protected $_CrudForm = null;
    abstract protected function _getCrudForm();

    protected $_messages = array(
        'add'    => 'Record added successfully',
        'edit'   => 'Record updated successfully',
        'delete' => 'Record deleted successfully'
    );

    public function CrudModel()
    {
        return $this->_CrudModel;
    }

    public function CrudForm()
    {
        return $this->_CrudForm;
    }    

    //must return a string. Used for form titles, like "edit <name>"
    protected function _getRecordName(array $row)
    {
        return print_r($row,1);
    }
    
    //must return a string. Used for form titles,
    protected $_separator = ' -> ';

    /***** default CRUD ACTIONS */

    protected function getFormOptions()
    {
        return array(); //return array('fixed_values'=>array(visible=1))
    }

    public function __construct(Zend_Controller_Request_Abstract $request, 
        Zend_Controller_Response_Abstract $response, 
        array $invokeArgs = array())
    {
        parent::__construct( $request,  $response,  $invokeArgs);

        // generate model
        $this->_CrudModel = $this->_getCrudModel();
        if (!($this->CrudModel() instanceof Crud_Model_Interface)){
            throw new Zend_Exception('object not an instance of Crud_Model_Interface');
        }
        $this->_setupPK();
        // generate form
        $this->_CrudForm = $this->_getCrudForm();
        if (!($this->CrudForm() instanceof Crud_Forms_Abstract)){
            throw new Zend_Exception('object not an instance of Crud_Forms_Abstract');
        }
        
        // 
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    public function preDispatch()
    {
        $controllerName = ucfirst($this->getRequest()->getControllerName());
        $actionName = ucfirst($this->getRequest()->getActionName());
        if ($actionName=='Index'){
           $actionName = 'Record list';
        }
        $this->view->title = sprintf(
            '<a href="%s">%s</a> %s %s',
            $this->view->url(
                array('module'=>'admin', 'controller'=>$this->getRequest()->getControllerName()),
                'default',true
            ),
            $controllerName,
            $this->_separator,
            $actionName
        );
    }

    /** Method called after $this->data is set. Use
    * <code>
    * foreach($this->view->data as &$row){
           //change $row['name'] = $row['name'];
      }
    * </code>
    */
    protected function _postIndex()
    {
        foreach ($this->view->data as &$row) {
        }
    }


    protected function _processOrderForm()
    {
       //order. If post redirect and set get variables, then use it when existing
       if (  Crud_Forms_Order_Helper::isFormPosted($this->getRequest()) ){
         $this->getHelper('redirector')->gotoUrl(
            Crud_Forms_Order_Helper::url($this->view, $this->getRequest())
         );
       }
       $orderQuery = Crud_Forms_Order_Helper::getOrderQuery($this->getRequest());
       $this->view->orderForm = $this->CrudForm()->getOrderForm($this->getRequest());
       return $orderQuery;
    }

    

    protected function _processFilterForm()
    {
        //filters
       if (Crud_Forms_Filter_Helper::isFormPosted($this->getRequest())) {
            $filtersWhereArray = Crud_Forms_Filter_Helper::arrayToWhereArray(
                $this->getRequest()->getPost(),
                $this->CrudModel()
            );
            $this->getHelper('redirector')->gotoUrl(
                Crud_Forms_Filter_Helper::url($this->view, $this->getRequest()->getPost())
            );
       }
       //set filters from the _GET vars
       $filterArray = Crud_Forms_Filter_Helper::stringToArray(
           $this->getRequest()->getParam('filter', '')
       );
       #pd($filterArray);
       $filtersWhereArray = Crud_Forms_Filter_Helper::arrayToWhereArray($filterArray, $this->CrudModel());
       $this->view->filterForm = $this->CrudForm()->getFilterForm($filterArray);
       return $filtersWhereArray;
    }

    
    public function indexAction()
    {
       // get flash msg from session
       $this->view->messages = $this->_flashMessenger->getMessages();
       $this->view->model = $this->CrudModel();


       $orderQuery = $this->_processOrderForm();
       $filtersWhereArray = $this->view->enabledFilters = $this->_processFilterForm();
       //pd($filtersWhereArray);
       //helper path
       $this->view->setHelperPath(
           realpath( dirname(__FILE__).'/../Forms/Helpers/List'), //to move
           'Crud_Forms_Helpers_List_'
       );
       
       //set adapter, paginator,
       if ($this->CrudModel() instanceof Crud_Model_DbTable_Abstract){
           $adapter = $this->CrudModel()->fetchPaginatorAdapter( 
               $filtersWhereArray, $orderQuery, array()
           );
       }
       /*if ($this->CrudModel() instanceof Crud_Model_Rest_Abstract){
           $adapter = $this->CrudModel()->fetchPaginatorAdapter(
               $filtersWhereArray, $orderQuery, $this->CrudModel()->fetchAll()
           );
       }*/
       $paginator = new Zend_Paginator($adapter);
       $paginator->setItemCountPerPage($this->_recordsPerPage);
       $page = $this->_request->getParam('page', 1);
       $paginator->setCurrentPageNumber($page);
       $this->view->paginator = $paginator;
       $this->view->controllerName = $this->getRequest()->getControllerName();

       //paginator -> data (for the view)
       $this->view->data = Crud_View_Helpers_Paginator::paginatorToArray($this->view->paginator);

       //post processing (custom when overloading)
       $this->_postIndex();

       //rendering with default HTML if the script doesn't exist
        try {
            $this->render();
        } catch (Exception $e) {

            Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
            if ($this->_useInternalListView){
                //trigger_error('index.phtml not found, default rendering', E_USER_NOTICE);
                if ($this->view->enabledFilters) {
                    echo count($this->view->enabledFilters) .'filter(s) enabled';
                }
                echo $this->view->orderForm;
                echo $this->view->filterForm;
                echo $this->view->paginationControl($this->view->paginator, 'Sliding', '_table_navigation.phtml');
                //display table with helper
                $columns = $this->CrudForm()->getColumns();
                echo Crud_View_Helpers_Paginator::paginatorToTable($this->view->data,$this->view, array_combine($columns, $columns) );
            } else {
                echo "index view script not found. see _useInternalListView to use internal render";
            }
        }
       
    }

    public function listAction()
    {
        $this->_forward('index');
    }

    public function addAction()
    {
        //$this->CrudForm()->submit->setLabel('Add');
        $this->view->form = $this->_CrudForm;
        
        if ($this->getRequest()->isPost()) {
            
            $formData = $this->getRequest()->getPost();
            
            if ($this->CrudForm()->isValid($formData)) {
                //get post data (except "id")
                $insertRow = array();

                $columns = $this->CrudForm()->getColumns();
                foreach($columns as $fieldName){
                    $insertRow[$fieldName] = $this->CrudForm()->getValue($fieldName);
                }
                
                $this->_preAdd();

                $insertRow = $this->_processPostData($insertRow);
                $insertRow = $this->_processFixedValues($insertRow);
                //insert
                try {
                    $this->CrudModel()->insert($insertRow);
                    $this->_postAdd();
                    $this->_flashMessenger->addMessage($this->_messages['add']);
                } catch (Zend_Db_Statement_Exception $e) {
                    $this->manageOperationException($e/*, array(
                        'prefix'        =>'Unable to update the form.',
                        'textError'     => 'Technical Details:' . $e->getMessage(),
                        'textErrorDuplicate'    =>'Duplicate record'
                    )*/);
                }
                //redirect
                $this->_redirectToControllerIndex();
                
            } else {
                $this->CrudForm()->populate($formData);
            }
        }
        

        //render with default HTML
        try {
            $this->render();
        } catch (Zend_view_Exception $e) {
            //only if add.phtml is not found !
            if (strpos($e->getMessage(),'add.phtml')!==false) {
                Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
                echo $this->view->form;
                echo '<a href="'
                     .$this->view->url(array(Crud_Config::PK_NAME=>null, 'action'=>'index'))
                     .'">Back to the listing</a>';
            } else { //other type of exception in the user-defined template
                throw new Zend_View_Exception($e->getMessage());
            }
        }
    }

    protected function _processPostData($post)
    {
        return $post;
    }

    protected function _processFixedValues($post)
    {
        $fixedVals = $this->CrudForm()->getFixedValues();
        $metadataKeys = array_keys($this->CrudModel()->getMetadata());
        #pd($metadataKeys);
        foreach ($fixedVals as $k=>$v) {
            if (in_array($k, $metadataKeys)) {
                $post[$k] = $v;
            } else {
                trigger_error(get_class() . ":fixed val $k not found in the metadata   ");
            }
        }
        return $post;
    }

    /**
     * Override to implement logic that needs to occur before an addition occurs
     *
     */    
    protected function _preAdd()
    {
        /* example for comments filtering
        $this->CrudForm()->isValid($this->getRequest()->getPost());
        $comment = $this->CrudForm()->getValue('comment');
        */
    }    

    protected function _postAdd()
    {

    }

    public static function parsePKValue($val)
    {
        if (strpos($val, '=')===false) {
            return $val;
        } else {
            parse_str($val, $ret);
            return $ret;
        }
    }

    /** return the primary key value from post data.
     * If the key is compound, an array with all the values from the postdata is returned
     *
     * @param Crud_Model_Interface $model
     * @param array $postData
     */
    public static function getNeededValuesForPK(
            Crud_Model_Interface $model, array $postData
    )
    {
        $pk = $model->getPKName();
        if (is_array($pk)) {
            foreach($pk as $k) {
                $ret[$k] = $postData[$k];
            }
        } else {
            $ret = $postData[$pk]; //maybe better to use form->getValue ?
        }
        //pd($ret);
        return $ret;
    }

    public function editAction()
    {
        $form = $this->view->form =$this->CrudForm();
        $model = $this->CrudModel();
        $this->view->errors = null;
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            //if there is post data, there is a record saving
            if ($form->isValid($formData)) {
                
                $primaryKeyValue = self::parsePKValue(
                    $this->_getParam(Crud_Config::PK_NAME)
                );
                $insertRow = array(/*'id' => $id*/); //????
                $columns = $this->CrudForm()->getColumns();
                
                foreach($columns as $fieldName){
                    $insertRow[$fieldName] = $form->getValue($fieldName);
                }            
                $this->_preEdit();
                $insertRow = $this->_processPostData($insertRow);
                $insertRow = $this->_processFixedValues($insertRow);

                //insert
                try {
                    $resultUpdate = $model->update($insertRow, $primaryKeyValue);
                    if ($resultUpdate) {
                        $this->_flashMessenger->addMessage($this->_messages['edit']);
                    } else {
                        throw new Zend_Db_Exception('No records changed'); //caught from next catch
                    }
                    $this->_postEdit($resultUpdate);
                } catch (Zend_Exception $e) {
                    $this->manageOperationException($e/*, array(
                        'prefix'        =>'Unable to update the form.',
                        textError'     => 'Technical Details:' . $e->getMessage(),
                        'textErrorDuplicate'    =>'Duplicate record'
                    )*/);
                }
                //redirect
                $this->_redirectToControllerIndex();
                
            } else {
                 // if the form is not valid, populate
                 $form->populate($formData);
            }
        } else {
                // no post data (when opening the page) => get the id & populate the form
                 $form->submit->setLabel('Save');
                 $primaryKeyValue = self::parsePKValue(
                     $this->_getParam(Crud_Config::PK_NAME)
                 );
                 if ($primaryKeyValue) {
                    $row = $model->getByPK($primaryKeyValue);
                    $form->populate($row);
                    $this->view->title2 .= $this->_separator
                                        . $this->_getRecordName($row);
                }
         }

        //render with default HTML
        try {
            $this->render();
        } catch (Zend_View_Exception $e) {
            if (strpos($e->getMessage(),'edit.phtml')!==false) {
                Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
                echo $this->view->errors;
                echo $this->view->form;
                echo '<a href="'
                     .$this->view->url(array(Crud_Config::PK_NAME=>null, 'action'=>'index'))
                     .'">Back to the listing</a>';
            } else { //other type of exception in the user-defined template
                throw new Zend_View_Exception($e->getMessage());
            }
        }

    }

    /** manage exceptions by setting $this->view->errors
     * and flashMessenger with errors
     *
     * @param Zend_Excetpion $e
     * @param array $options prefix,textError,textErrorDuplicate
     */
    public function manageOperationException(Zend_Excetpion $e, $options = null)
    {
        $prefix             = !empty($options['prefix']) ? $options['prefix'] : 'Operation not performed.';
        $textError          = !empty($options['textError']) ? $options['textError'] : 'Technical Details:' . $e->getMessage();
        $textErrorDuplicate = !empty($options['textErrorDuplicate']) ? $options['textErrorDuplicate'] : 'Duplicate entry';

        //compose message
        $message = sprintf('<span class="errors">%s %s</span>',
            $prefix,
            ($e->getCode()==23000) ? $textErrorDuplicate : $textError
        );
        //set view variable and flashMessenger
        $this->_flashMessenger->addMessage($message);
        $this->view->errors = $message;
    }

    /**
     * Override to implement logic that needs to occur before an edit occurs
     *
     */
    protected function _preEdit()
    {
    }
    
    protected function _postEdit()
    {

    }

    public function deleteAction()
    {
        if ($this->getRequest()->isPost()) {

            $action = $this->getRequest()->getPost('action', null);
            if ('delete'==$action){
                $this->getRequest()->getPost();
            }

            $del = $this->getRequest()->getPost('del');
            try {
                if ($del == 'Yes') {
                    $plVal = self::parsePKValue($this->getRequest()->getPost(
                        Crud_Config::PK_NAME
                    ));
                    $this->CrudModel()->delete($plVal);
                }
            } catch (Zend_Exception $e) {
                $this->manageOperationException($e/*, array(
                        'prefix'        =>'Unable to update the form.',
                        'textError'     => 'Technical Details:' . $e->getMessage(),
                        'textErrorDuplicate'    =>'Duplicate record'
                    )*/);
            }
            $this->_flashMessenger->addMessage($this->_messages['delete']);
            //redirect
            $this->_redirectToControllerIndex();
        } else {
            //get records from URL params
            $pkRawValue = $this->_getParam(Crud_Config::PK_NAME);
            $this->view->record = $this->CrudModel()->getByPK(
                self::parsePKValue($pkRawValue)
            );
            $this->view->primaryKeyOfTheRecord = $pkRawValue;
            
            //render the script, or a default text if not exists
            try {
                $this->render();
            } catch (Zend_View_Exception $e) {

                if (strpos($e->getMessage(),'delete.phtml')!==false) {
                    Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);

                    echo '<p>Are you sure to delete the record '
                    . $this->CrudModel()->getRecordHumanReadableName($this->view->primaryKeyOfTheRecord)
                    .' ? </p>
                    <form action="'.$this->view->url(array('action'=>'delete')) .'" method="post">
                    <div>
                      <input type="hidden" name="' . Crud_Config::PK_NAME . '" value="'. $this->view->primaryKeyOfTheRecord.'" />
                      <input type="submit" name="del" value="Yes" />
                      <input type="submit" name="del" value="No" />
                    </div>
                    </form>';
                    echo '<a href="'
                     .$this->view->url(array(Crud_Config::PK_NAME=>null, 'action'=>'index'))
                     .'">Back to the listing</a>';

                } else { //other type of exception in the user-defined template
                    throw new Zend_View_Exception($e->getMessage());
                }


                
            }

        }

        

    }

    protected function _redirectToControllerIndex()
    {
        $this->getHelper('redirector')->gotoUrl( $this->view->url(array(
             'action'   => 'index',
             'page'     => $this->getRequest()->getParam('page',null),
             Crud_Config::PK_NAME       => null
            ))
        );
    }
    

    protected function _noLayout()
    {
        //disable layout and view
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
        $layout = Zend_Layout::getMvcInstance();
        if ($layout instanceof Zend_Layout) {
            $layout->disableLayout(); //noLayout
        }
    }

    protected function _noView()
    {
        // no render for script view template
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);

    }

    protected function _setupPK()
    {
        $pk = $this->CrudModel()->info(Zend_Db_Table_Abstract::PRIMARY);
        $this->_modelPK = count($pk)==1 ? implode('', $pk) : $pk;
    }

}