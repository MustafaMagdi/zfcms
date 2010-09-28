<?php
class Ifrond_Model_Mapper
{
	/* @var _dbTable Ifrond_Model_Db_Table */
	protected $_dbTable = null;
	protected $_dbTableName = null;
	/* @var _itemClass Ifrond_Model_Item */
	protected $_itemClass = null;
	protected $_defaultOrder = 'id ASC';
	public function __construct ($table = null)
	{
		if ($table !== null) {
			$this->setDbTable($table);
		} elseif ($this->_dbTable !== null) {
			$this->setDbTable($this->_dbTable);
		} else {
			if ($this->_dbTableName !== null) {
				$this->setDbTable(new Ifrond_Model_Db_Table(array('name' => $this->_dbTableName)));
			} else {
				$className = get_class($this);
				$classNameDbTable = str_replace('_Mapper', '_DbTable', $className);
				$this->setDbTable($classNameDbTable);
			}
		}
		if ($this->_itemClass === null) {
			$className = get_class($this);
			$this->_itemClass = str_replace('_Mapper', '', $className);
		}
		$this->init();
	}
	public function init() {
		 
	}
	public function setDbTable ($dbTable)
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (! $dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}
	public function getDbTable ()
	{
		if (null == $this->_dbTable) {
			$this->setDbTable($this->_dbTable);
		}
		return $this->_dbTable;
	}
	public function save (array $row)
	{
		if (!isset($row['id'])) {
			$row['id'] = 0;
		} else {
			$row['id'] = intval($row['id']);
		}
		if ($row['id'] == 0) {
			unset($row['id']);
			$row['id'] = $this->getDbTable()->insert($row);
		} else {
			$this->getDbTable()->update($row, array('id = ?' => $row['id']));
		}
		return $row;
	}
	public function delete ($id)
	{
		$id = intval($id);
		if ($id > 0) {
			return $this->getDbTable()->delete(array('id = ?' => $id));
		} else {
			return false;
		}
	}
	public function _toObject (array $row)
	{
		$class = $this->_itemClass;
		$obj = new $class(array('row' => $row));
		$obj->setMapper($this);
		return $obj;
	}
	public function _toObjectSets ($rows)
	{
		if ($rows instanceof Zend_Db_Table_Rowset) {
			$rows = $rows->toArray();
		}
		foreach ($rows as $k => $t) {
			$rows[$k] = $this->_toObject($t);
		}
		return $rows;
	}
	public function find ($id)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return false;
		}
		$row = $result->current();
		return $this->_toObject($row->toArray());
	}
	public function __call ($name, $args)
	{
		if (substr($name, 0, 8) == 'getAllBy') {
			$field = strtolower(str_replace('getAllBy', '', $name));
			$val = $args[0];
			$select = $this->getDbTable()->select(); /* @var select Zend_Db_Table_Select */
			if (is_array($val)) {
				if (! isset($val['operator'])) {
					$val['operator'] = '=';
				}
				$select->where('`' . $field . '` ' . $val['operator'] . " ?", $val['value']);
				if (isset($val['limit']))
				$limit = intval($val['limit']);
				else
				$limit = 0;
				if ($limit > 0) {
					$select->limit($limit, 0);
				}
				if (isset($val['order'])) {
					$select->order($val['order']);
				} else {
					$select->order($this->_defaultOrder);
				}
			} else {
				$select->where("`" . $field . "` = ?", $val);
			}
			$result = $this->getDbTable()->fetchAll($select);
		} else {
			$result = $this->getDbTable()->$name($args);
		}
		//_p($select->assemble());
		//_p($result);
		if ($result instanceof Zend_Db_Table_Rowset) {
			$entries = array();
			foreach ($result as $t) {
				$entries[] = $this->_toObject($t->toArray());
			}
			return $entries;
		}
		if ($result instanceof Zend_Db_Table_Row) {
			return $this->_toObject($t->toArray());
		}
		return $result;
	}
	public function createNew (array $row = array())
	{
		return $this->_toObject($row);
	}
}