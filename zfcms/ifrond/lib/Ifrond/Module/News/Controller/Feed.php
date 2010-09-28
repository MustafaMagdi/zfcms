<?php

class Ifrond_Module_News_Controller_Feed extends Ifrond_Controller_Action_Public
{

	public function getModelMapperNews() {
		$d = new Ifrond_Module_News_Model_Mapper_News();
		return $d;
	}

	public function getModelMapperRef() {
		$d = new Ifrond_Module_Ref_Model_Mapper_Ref();
		return $d;
	}

	public function getModelThumb($path) {
		$d = new Ifrond_Model_Thumb($path);
		return $d;
	}

	public function indexAction()
	{
		$newsMapper = $this->getModelMapperNews();
		$newsMapper->setLimit(25);
		$news = $newsMapper->getAll();

	}

	public function rssAction()
	{
		$newsMapper = $this->getModelMapperNews();
		$newsMapper->setLimit(25);
		$news = $newsMapper->getAll();
		$feed = new Zend_Feed_Writer_Feed();
		$feed->setTitle('Губернатор и Правительство Ульяновской области');
		$feed->setLink('http://new.ulgov.ru/');
		$feed->setFeedLink('http://new.ulgov.ru/news/feed/rss/', 'rss');
		/*
		$feed->addAuthor(array(
		    'name'  => 'Пресс-служба Правительства Ульяновской области',
		    'email' => 'info@ulgov.ru',
		    'uri'   => 'http://ulgov.ru/'
		));
		*/
		$feed->setGenerator('Ifrond', '2', 'http://ifrond.com/');
		$feed->setDescription('Сообщения пресс-службы Губернатора и Правительства Ульяновской области', 'rss');
		$feed->setImage(array(
			'uri' => 'http://new.ulgov.ru/pub/images/rsslogo.gif',
			'title' => 'Губернатор и Правительство Ульяновской области',
			'link' => 'http://new.ulgov.ru/'
		));
		$feed->setDateModified(new Zend_Date($news[0]->date_edit, 'ru_RU'));
		
		foreach ($news as $t) {
			$entry = $feed->createEntry();
			$entry->setTitle(htmlspecialchars($t->title));
			$t->extend();			
			$dateEdit = new Zend_Date($t->date_edit, 'ru_RU');
			$dateEvent = new Zend_Date($t->date_event, 'ru_RU');
			$entry->setLink('http://new.ulgov.ru/news/'.$t->topic->extra1.'/'.$dateEvent->toString('ddMMYYYY').'/'.$t->id.'/');
			$entry->setId('http://new.ulgov.ru/news/index/permlink/id/'.$t->id.'/');
			$entry->setDateModified($dateEdit);
			$entry->setDateCreated($dateEvent);
			$entry->setTitle(htmlspecialchars($t->title));
			$entry->setDescription(htmlspecialchars($t->subtitle));			
			$entry->setContent(htmlspecialchars($t->text));
			$entry->addAuthor(array(
			    'name'  => 'Пресс-служба Правительства Ульяновской области',
		    	'email' => 'info@ulgov.ru',
		    	'uri'   => 'http://ulgov.ru/'
			));
			$entry->addCategory(array(
				'term' => $t->topic->extra1,
				'label' => $t->topic->title
			));
		    $feed->addEntry($entry);
		}
		$this->_helper->layout->disableLayout();
		$this->getResponse()->setHeader('Content-Type', 'application/rss+xml; charset=utf-8');
		$this->view->feed = $feed->export('rss');


	}

}