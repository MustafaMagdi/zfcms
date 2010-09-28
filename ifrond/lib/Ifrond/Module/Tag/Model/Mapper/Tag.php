<?php
class Ifrond_Module_Tag_Model_Mapper_Tag extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'tag';
	protected $_pmodule = null;

	public function setPmodule($val)
	{
		$this->_pmodule = $val;
	}

	public function getPmodule()
	{
		return $this->_pmodule;
	}

	public function getAttached($pid, $pmodule = null)
	{
		if ($pmodule === null) $pmodule = $this->getPmodule();
		$tagTable = $this->getTableName();
		$tagAttTable = $tagTable.'_att';
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
		->from(array('att' => $tagAttTable), array('*'))
		->join(array('tag' => $tagTable), '(`tag`.`id` = `att`.`tid`)', array('title'))
		->where('`att`.`pid` = ?', $pid)
		->where('`att`.`pmodule` = ?', $pmodule);
		//_p((string)$select);
		$rows = $db->query($select)->fetchAll();
		return $rows;
	}

	public function getModelAtt()
	{
		$d = new Ifrond_Module_Tag_Model_Mapper_TagAtt();
		$d->setPmodule($this->_pmodule);
		return $d;
	}

	public function getByTitle($title)
	{
		$table = $this->getDbTable();
		$select = $table->select()->where('title = ?', $title);
		$row = $table->fetchRow($select);
		if ($row == null) return false;
		$tag = $this->_toObject($row->toArray());
		return $tag;
	}

	public function reAttach($rows, $pid, $pmodule = null)
	{
		if ($pmodule === null) $pmodule = $this->getPmodule();		
		$atts = $this->getModelAtt();	
		$atts->unAttach($pid);
		$result = array();	
		foreach ($rows as $t) {
			$tag = $this->getByTitle($t);
			if ($tag === false) {
				$row = array();
				$row['title'] = $t;
				$tag = $this->createNew($row);
				$tag->save();
			}
			$row = $atts->attach($pid, $tag->id);	
			$row['title'] = $t;
			$result[] = $row;		
		}
		return $result;
	}


}