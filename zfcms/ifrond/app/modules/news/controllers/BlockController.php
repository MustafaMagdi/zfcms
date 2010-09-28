<?php
class News_BlockController extends Ifrond_Module_News_Controller_Block
{	
	
	public function getModelMapperNews() {
		$d = new Ifrond_Module_News_Model_Mapper_News();
		return $d;
	}
	
	public function getModelMapperRef() {
		$d = new Ifrond_Module_Ref_Model_Mapper_Ref();
		return $d;
	}	
	
	public function memorydatesAction()
	{
		$newsMapper = $this->getModelMapperNews();
		$limit = intval($this->_getParam('limit', 2));
		$newsMapper->setLimit($limit);
		$news = $newsMapper->getByTopicShortcut('rdates');		
		$this->view->news = $news;
	}
	public function importantAction()
	{		
		$newsMapper = $this->getModelMapperNews();
		$limit = intval($this->_getParam('limit', 3));
		$newsMapper->setLimit($limit);
		$news = $newsMapper->getByTopicShortcut('important');		
		$this->view->news = $news;
	}
	public function tapeAction()
	{
		$newsMapper = $this->getModelMapperNews();
		$limit = intval($this->_getParam('limit', 15));		
		$newsMapper->setLimit($limit);
		$news = $newsMapper->getAll();
		$this->view->news = $news;
	}
	public function calendarAction()
	{
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->viewRenderer('calendar-ajax');
		}
		$newsMapper = $this->getModelMapperNews();
		$year = clearStr($this->_getParam('year', date('Y')));
		$month = clearStr($this->_getParam('month', date('m')));
		$this->view->year = $year;
		$this->view->month = $month;
		$startDate = $year.'-'.$month.'-01 00:00:00';
		$endDate = $year.'-'.$month.'-31 23:59:59';
		$activeDates = $newsMapper->getActiveDates($startDate, $endDate);
		$this->view->activeDates = $activeDates;
	}
	public function topicsAction() {
		$refMapper = $this->getModelMapperRef();
		$refs = $refMapper->getAllByPid(array('value' => 1, 'order' => 'rang ASC'));		
		$this->view->topics = $refs;
	}
}