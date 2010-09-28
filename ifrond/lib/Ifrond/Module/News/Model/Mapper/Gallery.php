<?php
class Ifrond_Module_News_Model_Mapper_Gallery extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'att_image';
		
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
	
	public function getByNews($pid) {
		$table = $this->getDBTable();
		$select = $table->select()->from($table);
		$select->where('`pid` = ?', $pid);
		$select->where('`module` = ?', 'news');
		$select->order('rang ASC');
		//_p((string) $select);
		$rows = $table->fetchAll($select);
		$oRows = $this->_toObjectSets($rows);		
		return $oRows;
	}
}