<?php
class Ifrond_Module_Page_Model_Mapper_Page extends Ifrond_Model_Mapper
{
	/* @var _tree Ifrond_Tree */
	protected $_tree = null;
	protected $_defaultOrder = 'rang ASC';
	protected $_dbTableName = 'page';
	public function getTree ()
	{
		if ($this->_tree == null) $this->setTree();
		return $this->_tree;
	}
	public function setTree ()
	{
		$this->_tree = new Ifrond_Model_Tree();
		$this->_tree->setMapper($this);
		$this->_tree->getTree();
		return $this;
	}
	public function getChildByShortcut ($pid, $shortcut)
	{
		$select = $this->getDBTable()->select();
		$select->where('`shortcut` = ?', $shortcut)->where('`pid` = ?', $pid);
		$row = $this->getDBTable()->fetchRow($select);		
		if ($row == null) {
			return false;
		} else {
			$obj = $this->createNew($row->toArray());
		}		
		return $obj;
	}
	public function getByPath ($path, $id = 0)
	{
		if (! $this->_tree) {
			$this->setTree();
		}
		$row = $this->getTree()->find($path);
		if ($row === false) {
			$row = $this->getTree()->find(dirname($path));
			if ($row === false)
			return false;
			$obj = $this->getChildByShortcut($row['id'], basename($path));
		} else {
			$obj = $this->createNew($row);
			$obj->update();
		}
		return $obj;
	}
	public function getParents ($id)
	{
		if (! $this->_tree) {
			$this->setTree();
		}
		if ($id == 0)
		return false;
		$rows = $this->getTree()->getParents($id);
		$rows = $this->_toObjectSets($rows);
		return $rows;
	}
	public function getExtend($id) {
		
	}
	public function getNextRang($pid) {
		$table = $this->getDBTable();
		$select = $table->select()->from($table, array('m' => 'MAX(rang)'));
		$select->where('`pid` = ?', $pid);
		$row = $table->fetchRow($select);
		if ($row == null) {
			return 0;
		} else {
			return ($row->m + 1);
		}
	}
	public function getLasts($limit = 10) {
		$table = $this->getDBTable();
		$select = $table->select();
		$select->order('date_add DESC');
		$select->limit($limit);
		$rows = $table->fetchAll($select);
		return $this->_toObjectSets($rows);		
	}
	

}