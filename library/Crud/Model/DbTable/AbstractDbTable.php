<?php

namespace Crud\Model\DbTable;


/*
 * Extension of \Zend_Db_Table_Abstract that implements \Crud\Model\InterfaceModel
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
abstract class AbstractDbTable
    extends \Zend_Db_Table_Abstract
    implements \Crud\Model\InterfaceModel
{
    protected static $_firstElementSelect = array(0 => '--select one--');

    protected $_sqlAlias = null;

    /**
     * ctor
     *
     * @param array $config
     */
    public function __construct($config = array()) //needed ?
    {
        parent::__construct($config);
    }

    /**
     * force class to calculate metadata and returns it
     *
     * @return array
     */
    public function getMetadata()
    {
        if (empty($this->_metadata)) {
            $this->_setupMetadata();
        }
        return $this->_metadata;
    }

    /**
     * Get record name in a readable name (for delete action)
     *
     * @param string $pk
     * @return string
     */
    public function getRecordHumanReadableName($pk)
    {
        return 'with "Id" equal to ' . $pk;
    }

    /**
     * Return the prefix of the model table used in _getSelectForPaginator()
     *
     * @return string e.g: "b." or ""
     */
    public function getSqlPrefix()
    {
        return ($this->_sqlAlias) ? $this->_sqlAlias.'.' : '';
    }


    /*
     * Returns \Zend_Paginator_Adapter_DbTableSelect. Override to apply JOINs. 
     * Used from paginator
     *
     * @return \Zend_Db_Select
     *
     */
    protected function _getSelectForPaginator()
    {
        return $this->select();
    }

    /**
     * Return the primary key, or the string
     *  (if the primary key is not compound)
     *
     * @return array|string primary key of the model
     */
    public function getPKName()
    {
        $pk = $this->info(\Zend_Db_Table_Abstract::PRIMARY);
        if (count($pk)===1) {
            return implode('', $pk);
        } else {
            return $pk;
        }
    }

   /**
    * Return record by PK
    *
    * @param string|array $idOrWhere where or array to select.
    *        If a single value, "<primary key> = ?" is prefixed
    * @return array result record
    */
    public function getByPK($idOrWhere, $exceptions = true)
    {
        // if the argument doesn't contain "?" (not a "where")
        // then make the "where"
        $pkName = $this->getPKName();
        $row = $this->fetchRow($this->_convertWhere($idOrWhere));
        if (!$row) {
            if ($exceptions) {
                throw new \Zend_Db_Exception(
                    'Could not find row with PK '
                    . print_r($idOrWhere, 1)
                );
            } else {
                return array();
            }
        }
        
        return $row->toArray();
    }

    /**
     * Used for search action
     *
     * @param <type> $row
     * @return <type>
     */
    public function getNameAutoComplete($row)
    {
        return 'implement getNameAutoComplete in the model';
    }

    

    /**
     * return titles + array to make CSV
     *
     * @param \Zend_Db_Select $where
     * @return array array(titles and data from fetchall->toArray)
     */
    public function getForCSV($where = null)
    {
        $data = $this->fetchAll($where)->toArray();
        $titles = isset($data[0]) ? array_keys($data[0]) : array();
        return array($titles, $data);
    }

    /**
     * Return the \Zend_Paginator_Adapter_DbTableSelect
     *
     * @param array $filters
     * @param string $orderQuery
     * @param array $records
     * @return \Zend_Paginator_Adapter_DbTableSelect
     */
    public function fetchPaginatorAdapter(
        $filters=array(), 
        $orderQuery=null, 
        array $records=array())
    {
        $select = $this->_getSelectForPaginator();
        //add filters
        $select = \Crud\Forms\Filter\Helper::updateSelect($select, $filters);

        if (null != $orderQuery) {
            $select->reset(\Zend_Db_Select::ORDER);
            $select->order(array($orderQuery));
        }
        // create a new instance of the paginator adapter and return it
        $adapter = new \Zend_Paginator_Adapter_DbTableSelect($select);
        return $adapter;
    }

    /**
     * Read the format, than make the proper where statement
     *
     * @param string|int|array $where
     * @return string|array
     */
    protected function _convertWhere($where)
    {
        $ret = $where;
        if (is_array($where)) { //compound key
            foreach ($where as $k=>$v) {
                $whereTwo[$k.' = ?'] =  $where[$k];
            }
            $ret = $whereTwo;
        } else {
            $pkName = $this->getPKName();
            if (is_array($pkName)) {
                throw new \Zend_Exception(
                    'update of record with compound key with no multiple values'
                );
            } else {
                //single argument with condition
                if (
                    strpos($where, '=')!==false
                    || strpos($where, '<')!==false
                    || strpos($where, '>')!==false
                ) {
                } else { //only values -> prefix PK
                    $ret = $this->getDefaultAdapter()->quoteIdentifier($pkName) . "='". $where."'"; //TODO quote
                }
            }
        }
        return $ret;
    }

    /**
     * delete record by where condition (array|string|int)
     *
     * @param array|string|int $where
     * @return int
     */
    public function delete($where)
    {
        return parent::delete($this->_convertWhere($where));
    }

    /**
     * update record 
     *
     * @param array $data
     * @param array|string|int $where
     * @return int
     */
    public function update(array $data, $where)
    {
        $ret = parent::update($data, $this->_convertWhere($where));
        return $ret;
    }


    /**
     * Used for ajax actions. To override in subclasses
     *
     * @param <type> $q
     * @param <type> $limit
     * @return <type>
     */
    public function search($q, $limit)
    {
        return array(
            'id'=>'qazwsxedcrfvtgbyhnujmikolp',
            '_name'=>'model::search to implement'
        );
        /* $select = $this->select();
         $select->from(
            $this->_name,
            array('id', '_name'=>new \Zend_Db_Expr("CONCAT(firstn ', lastn)")))
         ->orWhere(" email LIKE '%$q%'")
         ->orWhere(" firstname LIKE '%$q%'")
         ->orWhere(" lastname LIKE '%$q%'")
         ->limit($limit);

         return  $this->fetchAll($select)->toArray();
         */

    }

    /**
     * Return \Zend_Db_Select for dropdown of parent tables,
     * see  getForDropDown
     *
     * @return \Zend_Db_Select
     */
    protected function _getSelectForDropDown()
    {
        return $this->select();
    }

    /** Fetches the select returned by _getSelectForDropDown,
     *  and returns and array with keys=1st column and values=2nd col
     *
     * @param string $option
     * @param string $value
     * @return array
     */
    public function getForDropDown($option=null, $value=null)
    {
        $ret = array(0=>'-- select one --');
        $records = $this->fetchAll($this->_getSelectForDropDown())->toArray();
        foreach ($records as $record) {
            $finalOption = $option ? $option : array_shift($record);
            $finalValue  = $value  ? $value  : array_shift($record);
            $ret[$finalOption] = $finalValue .' (Id = '.$finalOption.')';
        }
        return $ret;
    }

    /**
     * Return full class name of subclass
     * e.g. Admin\Models\Translations
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }

    
}