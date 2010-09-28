<?php
class Ifrond_Module_News_Model_Mapper_Subscribe extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'subscribe';
	
	public function getByEmail($email) {
		$rows = $this->getAllByEmail($email);		
		return $rows;
	}
	
	public function getByKey($key, $id) {
		$select = $this->getDbTable()->select();
		$select->where("`id` = ?", $id);	
		$select->where("`keycode` = ?", $key);	
		$result = $this->getDbTable()->fetchRow($select);
		if (!$result) return false;
		return $this->_toObject($result->toArray());
	}
	
	
	
}