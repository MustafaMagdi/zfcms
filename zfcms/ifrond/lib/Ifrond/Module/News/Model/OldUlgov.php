<?php
class Ifrond_Module_News_Model_OldUlgov extends Ifrond_Model_Item
{
	
	public function getTopics() {
		$topics = array();
		$topics['2085']  = '1'; //Важное
		$topics['1229']  = '2'; //Новости региона
		$topics['1231']  = '3'; //Губернатор
		$topics['1230']  = '4'; //Правительство
		$topics['2186']  = '5'; //Памятные даты
		$topics['2579']  = '6'; //Приглашения для СМИ
		$topics['2332']  = '7'; //Новости сайта
		$topics['16313'] = '8'; //Горячие линии
		return $topics;
	}

	public function setTopicByPid($pid) {
		$topics = $this->getTopics();
		return $topics[$pid];
	}
	
	public function getModelMapperNews() {
		$d = new Ifrond_Module_News_Model_Mapper_News();
		return $d;
	}

	public function getModelNews() {
		$d = new Ifrond_Module_News_Model_News();
		return $d;
	}

	public function convert() {
		$newsMapper = $this->getModelMapperNews();
		$row['pid'] = $this->setTopicByPid($this->pid);
		$row['author'] = 1;
		$row['editor'] = 1;
		$row['is_active'] = $this->active;
		$row['date_add'] = $this->date;
		if ($this->editeddate != '0000-00-00 00:00:00') {
			$row['date_edit'] = $this->editeddate;
		} else {
			$row['date_edit'] = $this->date;
		}
		$row['date_event'] = $this->date;
		$row['title'] = $this->title;
		$row['subtitle'] = $this->subtitle;
		$row['text'] = $this->text;
		$row['shortcut'] = $this->shortcut;
		$news = $newsMapper->createNew($row);
		$news->save();
	}

}