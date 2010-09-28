<?php
class Ifrond_Module_Page_Model_OldUlgov extends Ifrond_Model_Item
{	
	public function getModelMapperPage() {
		$d = new Ifrond_Module_Page_Model_Mapper_Page();
		return $d;
	}

	public function getModelPage() {
		$d = new Ifrond_Module_Page_Model_Page();
		return $d;
	}

	public function convert($pid) {
		$pageMapper = $this->getModelMapperPage();
		$row['pid'] = $pid;
		$row['is_active'] = $this->active;
		$row['is_cat'] = $this->cated;
		$row['title'] = $this->title;
		$row['subtitle'] = $this->subtitle;
		$row['text'] = $this->text;
		$row['date_add'] = $this->date;
		if ($this->editeddate != '0000-00-00 00:00:00') {
			$row['date_edit'] = $this->editeddate;
		} else {
			$row['date_edit'] = $this->date;
		}
		$row['rang'] = $this->rang;
		$row['author'] = 1;		
		$row['editor'] = 1;
		$row['shortcut'] = $this->shortcut;
		$row['md5sc'] = md5($this->shortcut);		
		$page = $pageMapper->createNew($row);
		$page->save();
		return $page;
	}

}