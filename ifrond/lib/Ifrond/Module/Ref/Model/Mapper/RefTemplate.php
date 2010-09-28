<?php
class Ifrond_Module_Ref_Model_Mapper_RefTemplate extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'ref_template';

	public function getAll() {
		$table = $this->getDbTable();
		$select = $table->select()->order('rang ASC');
		$rows = $table->fetchAll($select);		
		return $this->_toObjectSets($rows);
	}
}