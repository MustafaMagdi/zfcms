<?php
class Ifrond_Module_Ref_Model_Mapper_Ref extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'ref';	

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
	
}