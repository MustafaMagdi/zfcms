<?php
class Ifrond_Module_News_Model_Mapper_OldUlgov extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'ugitems';	
	protected $_limit = 1000;	
	
	
	public function convertByPid($pid) {
		$newses = $this->getAllByPid($pid);
		$topic = $this->setTopicByPid($pid);
		foreach ($newses as $t) {
			$t->convert($topic);
		}		
	}
	
	public function convertAll($page = 1) {
		$topics = array(2085, 1229, 1231, 1230, 2186, 2579, 2332, 16313);		
		$select = $this->getDBTable()->select();
		$select->where("`pid` IN (".implode(', ', $topics).")");
		$select->limitPage($page, $this->_limit)->order('id ASC');
		$rows = $this->getDbTable()->fetchAll($select);		
		if (sizeof($rows) > 0) {
			$rows = $this->_toObjectSets($rows);
			foreach ($rows as $t) {
				$t->convert();
			}
			return true;
		} else {
			return false;
		}
	}
	
}