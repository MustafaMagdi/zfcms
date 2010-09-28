<?php
class Ifrond_Module_News_Model_Mapper_News extends Ifrond_Model_Mapper
{
	protected $_dbTableName = 'news';
	protected $_limit = 50;
	protected $_pages = null;

	public function getByTopic($pid, $page = 1) {
		$search = array();
		$search['pid'] = $pid;
		return $this->getByFilter($search, $page);
	}

	public function getModelMapperRef() {
		$d = new Ifrond_Module_Ref_Model_Mapper_Ref();
		return $d;
	}

	public function getByTopicShortcut($shortcut, $page = 1) {
		$refMapper = $this->getModelMapperRef();
		$topic = $refMapper->getAllByExtra1($shortcut);
		if ($topic != false) {
			return $this->getByTopic($topic[0]->id, $page);
		} else {
			return false;
		}
	}

	public function getByOldTopicAndShortcut($topic, $shortcut) {
		$refMapper = $this->getModelMapperRef();
		$topic = $refMapper->getAllByExtra1($topic);
		if ($topic != false) {
			$select = $this->getDBTable()->select();
			$select->where('`shortcut` = ?', $shortcut);
			$select->where('`pid` = ?', $topic[0]->id);
			$select->order('date_event DESC');
			$select->limit(1);
			//_p((string)$select);
			$row = $this->getDbTable()->fetchAll($select);
			$rows = $this->_toObjectSets($row);
			if (isset($rows[0])) return $rows[0];
			else return false;
		} else {
			return false;
		}
	}

	public function getActiveDates($startDate, $endDate, $topics = array()) {
		$select = $this->getDBTable()->select();
		$select->distinct();
		$select->from($this->getDBTable()->getTableName(),'DATE(`date_event`) AS p');
		if (sizeof($topics) > 0) {
			$select->where('`pid` IN (' . implode ( ', ', $topics ) . ')');
		}
		$select->where("`date_event` BETWEEN '"
		. toFormatDate($startDate, 'Y-m-d H:i:s')
		. "' AND '" . toFormatDate($endDate, 'Y-m-d H:i:s') . "'");
		$select->order('date_event ASC');
		$rows = $this->getDbTable()->fetchAll($select);
		return $rows;
	}	

	public function getByDate($date, $page = 1) {
		$search = array();
		$search['date_event'] = $date;
		return $this->getByFilter($search, $page);
	}

	public function getAll($page = 1) {
		return $this->getByFilter(array(), $page);
	}
	
	public function getByFilter($search = array(), $page = 1) {
		$select = $this->getDBTable()->select();
		if (isset($search['date_start'])) {
			$select->where('`date_event` > ?', $search['date_start']);
		}
		if (isset($search['date_end'])) {
			$select->where('`date_event` < ?', $search['date_end']);
		}
		if (isset($search['date_event'])) {
			$select->where('DATE(`date_event`) = ?', $search['date_event']);
		}
		if (isset($search['is_active'])) {
			$select->where('`is_active` = ?', $search['is_active']);
		}
		if (isset($search['pid'])) {
			$select->where('`pid` = ?', $search['pid']);
		}
		if (isset($search['title']) && $search['title'] != '') {
			$search['title'] = '%'.$search['title'].'%';
			$select->where("`title` LIKE '".$search['title']."' OR
			`subtitle` LIKE '".$search['title']."' OR
			`text` LIKE '".$search['title']."'");
		}
		$this->getPagesCount($select);
		if (isset($search['order'])) {
			$select->order($search['order']);
		} else {
			$select->order('date_event DESC');
		}
		$select->limitPage($page, $this->_limit);
		$rows = $this->getDbTable()->fetchAll($select);
		return $this->_toObjectSets($rows);
	}

	public function getPagesCount($select = null) {
		$table = $this->getDbTable();
		if ($select == null) {
			$selectPage = $table->select();
		} else {
			$selectPage = clone $select;
		}
		$selectPage->from($table->getTableName(),'COUNT(*) AS num');
		$num = $table->fetchRow($selectPage)->num;
		$this->_pages = ceil( $num / $this->_limit );
		return $this->_pages;
	}

	public function getPagesNum() {
		return $this->_pages;
	}

	public function setLimit($limit) {
		$this->_limit = $limit;
	}

}