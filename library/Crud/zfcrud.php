<?php
/** Tool to create CRUD controllers, scripts, forms, models
 *  Alpha version
 * 
 * - Database connection needed (using localhost, root, no password)
 * - Zend Framework libs must be added to include_path of php CLI
 * - leave in this directory
 * example of usage
 *
 * php -f zfcrud.php create crud Categories --table=categories --database=mydb
 * php -f zfcrud.php create crud Categories -t categories -d mydb
 * 
 */

//add path manually
set_include_path(realpath(__DIR__.'/../'));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

define('APP_MODULE', 'admin'); //TODO change controller template

// declare CL options
$zcg = new \Zend_Console_Getopt(array(
    //'module|m=s'     => 'Module name (e.g.: admin)',
    'table|t=s'    => 'Mysql table name',
    'f|force'      => 'Force writing (overrides existing files)',
    'database|d=s' => 'Database name'
));

try {
    $zcg->parse();
    //get options and launch CrudCreator class methods
    $args = $zcg->getRemainingArgs();
    if (empty($args[0])) {
        throw new \Zend_Console_Getopt_Exception('missing params');
    }
} catch (\Zend_Console_Getopt_Exception $e) {
    echo "example: php -f zfcrud.php create crud Categories --table=categories --database=mydb\n". $e->getUsageMessage();
    exit;
}

if (strcasecmp($args[0], 'create')===0) {
   if (strcasecmp($args[1], 'crud')===0) {
      $crudAction = ucfirst($args[2]);
       if (!$crudAction) {
           exit('action name not specified');
       }
       echo 'Creating crud files for '.$crudAction ." ...\n";
       $cc = new CrudCreator(
           $crudAction, APP_MODULE, $zcg->table, $zcg->database, $zcg->force
       );
       $cc->generateController();
       $cc->generateScripts();
       $cc->generateForms();
       $cc->generateDbTable();
   }

}

/**
 *
 * CrudCreator 
 *
 *
 *
 */
class CrudCreator
{
    private $_name;
    private $_appPath;
    private $_table;
    private $_metadata;
    private $_templates;
    private $_overwrite;


    /**
     * set initial params and read table metadata
     * 
     * @param string $name
     * @param string $module
     * @param string $table
     * @param string $db
     */
    function __construct($name, $module, $table, $db, $overwrite = false)
    {
        $this->_name        = ucfirst($name);
        $this->_appPath     = realpath(__DIR__ . '/../../application');
        $this->_table       = $table;
        $this->_overwrite   = $overwrite;

        $db = \Zend_Db::factory(
            'Pdo_Mysql',
            array(
                'host' => 'localhost',
                'username' => 'root',
                'dbname' => $db,
                'password' => ''
            )
        );
        //$db->getConnection();
        $this->_metadata = $db->describeTable($table);
        $this->initTpls();
    }


    /**
     *
     */
    function generateController()
    {
        $fileName = "{$this->_appPath}/modules/admin/"
                  . "controllers/{$this->_name}Controller.php";
        $data = $this->_templates['controller'];
        $data = str_replace(array('XXX'), array($this->_name), $data);
        $this->write($fileName, $data);

    }


    /**
     *
     */
    function generateScripts()
    {
        $scriptName = ltrim(
            strtolower(
                preg_replace('/([A-Z])/', '-$1', $this->_name)
            ),
            '-'
        );
        $fileName = "{$this->_appPath}/modules/admin/"
                  . "views/scripts/$scriptName/index.phtml";

        $keys = array();
        foreach ($this->_metadata as $k=>$v) {
            $keys[$k] = ucwords(str_replace(array('_', '-'), ' ', $k));
        }

        $data = str_replace(
            array('[KEYS]'),
            array(var_export($keys, 1)),
            $this->_templates['script']
        );
        $this->write($fileName, $data);
    }


    /**
     * launch generateForm, generateOrderForm and generateFilterForm
     */
    function generateForms()
    {
        $this->generateForm();
        $this->generateOrderForm();
        $this->generateFilterForm();
    }


    /**
     * generateForm
     */
    function generateForm()
    {
        $fileName = "{$this->_appPath}/library/Admin/Forms/{$this->_name}.php";
        $columns = implode(', ', array_keys($this->_metadata));
        $data = str_replace(
            array('[XXX]', '[COLUMNS]'),
            array($this->_name, $columns),
            $this->_templates['form']
        );
        $this->write($fileName, $data);
    }


    /**
     *generateOrderForm
     */
    function generateOrderForm()
    {
        $fileName = "{$this->_appPath}/library/Admin/Forms/Order/{$this->_name}.php";
        $data = str_replace(
            array('[XXX]'),
            array($this->_name),
            $this->_templates['orderform']
        );
        $this->write($fileName, $data);
    }


    /**
     *generateFilterForm
     */
    function generateFilterForm()
    {
        $fileName = "{$this->_appPath}/library/Admin/Forms/Filter/{$this->_name}.php";
        $data = str_replace(
            array('[XXX]'),
            array($this->_name),
            $this->_templates['filterform']
        );
        $this->write($fileName, $data);
    }


    /**
     *generateDbTable
     */
    function generateDbTable()
    {
        $fileName = "{$this->_appPath}/library/Admin/Models/{$this->_name}.php";
        

        $primary = array();
        foreach ($this->_metadata as $k=>$v) {
            if (!empty($k['PRIMARY'])) {
                $primary[] = $k;
            }
        }
        $primary = count($primary)==1 
                 ? "'" . array_shift($primary) . "'"
                 : var_export($primary, 1);

        $data = str_replace(
            array('[XXX]', '[TNAME]', '[PRIMARY]'),
            array($this->_name, $this->_table, $primary),
            $this->_templates['table']
        );
        $this->write($fileName, $data);
    }

    /**
     *
     * @param <type> $file
     * @param <type> $content 
     */
    function write($file, $content)
    {
        //die($file);
        if (!$this->_overwrite && file_exists($file)) {
            echo "$file already exists!\n";
        } else {
            $dir = dirname($file);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($file, $content);
            echo "written $file\n";
        }

    }


    /**
     * TPLS !
     */
    function initTpls()
    {

    $this->_templates['controller'] =  <<<DELIM
<?php

class Admin_XXXController extends \Crud\Controller\AbstractCrudController
{

    //protected \$_useInternalListView = true;
    protected \$_recordsPerPage = 15;

    protected function _getCrudModel()
    {
        return new \Admin\Models\XXX();
    }

    protected function _getCrudForm()
    {
        return new \Admin\Forms\XXX();
    }

    public function init()
    {
        /* Initialize action controller here */
    }

}
DELIM;

    $this->_templates['script'] = <<<DELIM
<?php if (\$this->messages): ?>
 <div class="flashMessages"><ul>
   <li><?php echo implode('</li><li>', \$this->messages) ?></li>
 </ul></div>
<?php endif; ?>
<?php echo \$this->orderForm ?>
<?php echo \$this->filterForm ?>
<?php echo \$this->paginationControl(
    \$this->paginator,
    'Sliding',
    '_table_navigation.phtml'
);

echo Crud\View\Helpers\\Paginator::paginatorToTable(
    \$this->data,
    \$this,
    [KEYS]
    ,
    array(), //options
    \$this->model
);
echo \$this->paginationControl(
    \$this->paginator,
    'Sliding',
    '_table_navigation.phtml'
);
?>
<a href="<?php echo \$this->url(array('action'=>'add'));?>" class="addlink">
<?php echo \$this->translate('Add a new record') ?></a>
DELIM;


    $this->_templates['form'] = <<<DELIM
<?php

namespace Admin\Forms;

use Admin;

class [XXX] extends \Crud\Forms\AbstractForm
{
    //protected \$_whiteListElements = array();

    public function init()
    {
        //... additional code ...
        parent::init();
    }


    protected function getModel()
    {
        return new \Admin\Models\[XXX]();
    }


    public function getOrderForm(\$formValues)
    {
        return new Order\[XXX](
            null,
            \$this->_model,
            \$formValues
        );
    }

    public function getFilterForm(\$formValues)
    {
        return new Filter\[XXX](
            null,
            \$this->_model,
            \$formValues
        );
    }

    protected function _add_custom_elements()
    {
        //COLUMNS = [COLUMNS]

        /*
        \$parentModel = new Models\**REPLACEME**();
        \$elem = new \Zend_Form_Element_Select(
            '**COLUMN**',
            array('label'=>**REPLACEME**)
        );
        \$elem->setMultiOptions(\$parentModel->getForDropDown());
        \$this->_formElements['**COLUMN**'] = \$elem;
        */


    }

}
DELIM;
    $this->_templates['orderform'] = <<<DELIM
<?php

namespace Admin\Forms\Order;

class [XXX] extends \Crud\Forms\Order\AbstractOrder
{

    /*protected function getDropDownValues()
    {
        return array();
    }*/

}
DELIM;

    $this->_templates['filterform'] = <<<DELIM
<?php

namespace Admin\Forms\Filter;

class [XXX] extends \Crud\Forms\Filter\AbstractFilter
{
   //protected \$_whitelist = array();

}
DELIM;


    $this->_templates['table'] = <<<DELIM
<?php

namespace Admin\Models;

class [XXX] extends \Crud\Model\DbTable\AbstractDbTable
{
    protected \$_name = '[TNAME]';
    protected \$_primary = [PRIMARY];

    /*
    public function _getSelectForPaginator()
    {
        \$select = \$this->select()
            ->setIntegrityCheck(false)
            ->from(array('t' => \$this->_name))
            ->joinLeft(
               array('p'=>'parent_table'),
               't.column_id = p.column_id',
               array('NEW_NAME' => 'PARENT_TABLE_NAME')
            );
        return \$select;
    }
    */

    /*
    protected function _getSelectForDropDown()
    {
        return \$this->select()->from(
            \$this->_name,
            array('categories_id', 'name')
        );
    }
    */


}
DELIM;


    }

}