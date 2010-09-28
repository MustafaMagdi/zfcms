<?php
class Ifrond_Module_Block_Model_Mapper_Block extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'block';	
	
	public function getAll() {
		$table = $this->getDbTable();
		$select = $table->select()->order('rang ASC');
		$select->where('is_active = ?', 1);
		$rows = $table->fetchAll($select);		
		return $this->_toObjectSets($rows);
	}
	public function getByShortcut($shortcut) {
		$table = $this->getDbTable();
		$select = $table->select()->order('rang ASC');
		$select->where('is_active = ?', 1);
		$select->where('shortcut = ?', $shortcut);
		$rows = $table->fetchRow($select);
		return $this->_toObject($rows->toArray());
	}
	
	
	
}