<?php
class Ifrond_Module_Search_Model_Mapper_Search
{
	protected $_tables = array('page', 'news');
	protected $_query = '';
	protected $_limit = 50;
	protected $_periodStart = '';
	protected $_periodEnd = '';
	protected $_order = 'ordered DESC';
	protected $_rowsCount = 0;
	
		
	public function setPage() {
		$options = array();
		$options['from'] = array(
			'id' => 'id',
			'title' => 'title',
			'subtitle' => 'subtitle',
			'ordered' => 'date_add',
			'tablename' => new Zend_Db_Expr("'page'")
		);		
		$options['in'] = array(	'title', 'subtitle', 'text');
		$table = 'page';
		return $this->getSelect($table, $options);
	}
	
	public function setNews() {
		$options = array();
		$options['from'] = array(
			'id' => 'id',
			'title' => 'title',
			'subtitle' => 'subtitle',
			'ordered' => 'date_event',
			'tablename' => new Zend_Db_Expr("'news'")
		);		
		$options['in'] = array(	'title', 'subtitle', 'text');
		$table = 'news';
		return $this->getSelect($table, $options);
	}
	
	public function getSelect($table, $options = array()) {		
		$table = new Ifrond_Model_Db_Table(array('name' => $table));		
		$select = $table->select();		
		$whereQuery = array();
		foreach ($options['in'] as $t) {
			$whereQuery[] = "`".$t."` LIKE '%".$this->_query."%'";
		}
		$select->where(implode(' OR ', $whereQuery));
		if ($this->_periodStart != '' && $this->_periodEnd != '') {
			$select->where("`".$options['from']['ordered']."` 
							BETWEEN '"
							.$this->_periodStart->toString('YYYY-MM-dd HH:mm:ss')
							."' AND '"
							.$this->_periodEnd->toString('YYYY-MM-dd HH:mm:ss')."'");
		}
		$this->_rowsCount = $this->_rowsCount + $this->getRowsCount($table, $select);
		$select->from($table, $options['from']);
		return $select;		
	}
	
	public function find($query, $page = 1) {
		$this->setQuery($query);
		$selectTables = array();
		//_p($this->_tables);
		foreach ($this->_tables as $t) {
			$method = 'set'.ucfirst($t);
			$selectTables[] = $this->$method();
		}
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$select = $db->select()->union($selectTables);
		
		$select->order($this->_order);
		$select->limitPage($page, $this->_limit);
		//_p((string)$select);
		$rows = $db->fetchAll($select);		
		return $rows;
	}
	
	public function getRowsCount($table, $select) {		
		$selectSearch = clone $select;		
		$selectSearch->from($table->getTableName(),'COUNT(*) AS num');
		
		$num = $table->fetchRow($selectSearch)->num;		
		return $num;
	}
	
	public function getPageNum() {
		$pages = ceil( $this->_rowsCount / $this->_limit );	
		return $pages;
	}
	
	public function setModule($module) {		
		if (is_array($module)) $this->_tables = $module;
		if (is_string($module)) $this->_tables = array($module);
	}
	
	public function setPeriod($start, $end) {
		$this->_periodStart = $start;
		$this->_periodEnd = $end;
	}
	
	public function setLimit($limit) {
		$this->_limit = $limit;
	}
	
	public function setQuery($query) {
		$this->_query = $query;
	}
	
	
}