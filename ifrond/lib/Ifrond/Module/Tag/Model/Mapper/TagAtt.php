<?php
class Ifrond_Module_Tag_Model_Mapper_TagAtt extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'tag_att';
	protected $_pmodule = null;

	public function setPmodule($val)
	{
		$this->_pmodule = $val;
	}

	public function getPmodule()
	{
		return $this->_pmodule;
	}

	public function unAttach($pid, $pmodule = null)
	{
		if ($pmodule === null) $pmodule = $this->getPmodule();
		$wheres = array();
		$table = $this->getDbTable();
		$db = $table->getAdapter();
		$wheres[] = $db->quoteInto('pid = ?', $pid);
		$wheres[] = $db->quoteInto('pmodule = ?', $pmodule);
		$result = $table->delete($wheres);
		return $result;
	}

	public function attach($pid, $tid, $pmodule = null)
	{
		if ($pmodule === null) $pmodule = $this->getPmodule();
		$row = array();
		$row['pid'] = $pid;
		$row['tid'] = $tid;
		$row['pmodule'] = $this->_pmodule;
		$att = $this->createNew($row);
		$att->save();
		return $att->getRow();
	}

}