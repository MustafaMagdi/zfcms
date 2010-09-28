<?php
class Ifrond_Module_Page_Model_Mapper_OldUlgov extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'ugitems';	
	
	public function convertAll($oldPid = 0, $newPid = 0) {
		$news = array(2085, 1229, 1231, 1230, 2186, 2579, 2332, 16313);
		if (!in_array($oldPid, $news)) {
			$rows = $this->getAllByPid(array('value' => $oldPid, 'order' => 'id ASC', 'limit' => 100));
			if (sizeof($rows) > 0) {			
				foreach ($rows as $t) {
					$newPage = $t->convert($newPid);
					$this->convertAll($t->id, $newPage->id);
				}
			}
		}
	}
	
}