<?php
class Ifrond_Module_Page_Model_Page extends Ifrond_Model_Item
{
	public function getParent ()
	{
		$result = $this->getMapper()->find($this->pid);
		return $result;
	}
	public function getChilds ($args = array())
	{
		$select = array('value' => $this->id);
		$select['order'] = 'rang ASC';
		if (sizeof($args) > 0) {
			if (isset($args[0])) $val = $args[0];
			else $val = $args;
			if (isset($val['limit']) && $val['limit'] > 0) {
				$select['limit'] = $val['limit'];
			}
			if (isset($val['order']) && $val['order'] != '') {
				$select['order'] = $val['order'];
			}
		}		
		$result = $this->getMapper()->getAllByPid($select);
		return $result;
	}
	public function newChild (array $row = array())
	{
		$row['pid'] = $this->id;
		return $this->getMapper()->createNew($row);
	}
	public function moveTo (integer $pid)
	{
		if ($pid > 0) {
			$this->pid = $this->id;
			$this->save();
		}
		return $this;
	}
	public function copyTo (integer $pid)
	{
		$obj = $this->copy();
		$obj->moveTo($pid);
		return $obj;
	}
	public function getParents ()
	{
		if ($this->pid == 0) return false;
		$rows = $this->getMapper()->getParents($this->pid);
		return $rows;
	}
	public function getExtend ()
	{
		$rows = $this->getMapper()->getExtend($this->id);
		return $rows;
	}

	public function isUniqueShortcut ($shortcut)
	{
		$table = $this->getMapper()->getDBTable();
		$select = $table->select();
		$select->where('`pid` = ?', $this->pid)
		->where('`shortcut` = ?', $shortcut)
		->where('`id` != ?', $this->id);
		$rows = $table->fetchAll($select);
		if (count($rows) > 0) return false;
		else return true;
	}

	public function checkShortcut ()
	{
		$sc = $this->shortcut;
		$scParts = explode(' ', $sc);
		$scParts = array_slice($scParts, 0, 3);
		$sc = implode(' ', $scParts);
		$scU = $sc;
		$i = 0;
		while (!$this->isUniqueShortcut($scU)) {
			$i++;
			$scU = $sc.'_'.$i;
		}
		$this->shortcut = $scU;
		$this->md5sc = md5($this->shortcut);
	}

	public function prepare () {
		$this->date_edit = date('Y-m-d H:i:s');
		if ($this->id == 0) $this->date_add = date('Y-m-d H:i:s');
		$this->checkShortcut();		
	}
	
	public function setTags($tagsList) {		
		$tags = $this->getModelMapperTag();		
		$this->tags = $tags->reAttach($tagsList, $this->id);
	} 
	
	public function getModelMapperTag() {
		$d = new Ifrond_Module_Tag_Model_Mapper_Tag();
		$d->setPmodule('page');
		return $d;
	}
	
	public function getTags() {
		$tags = $this->getModelMapperTag();
		$tagsList = $tags->getAttached($this->id);
		if (sizeof($tagsList) > 0) {
			$this->tags = $tagsList;
		}		
		return $tagsList;
	
	}

}