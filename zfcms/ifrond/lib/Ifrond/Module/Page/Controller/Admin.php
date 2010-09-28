<?php
class Ifrond_Module_Page_Controller_Admin extends Ifrond_Controller_Action_Admin
{
	/* @var pages Page_Model_Mapper_Page */
	protected $pages;

	public function indexAction()
	{
		
	}

	public function childsAction()
	{
		$pages = $this->getModelMapperPage();
		$pid = intval($this->_getParam('pid', 0));
		$page = $pages->createNew(array('id' => $pid));
		$this->view->pid = $pid;
		$this->view->pages = $page->getChilds();
	}

	public function getModelMapperPage() {
		$d = new Ifrond_Module_Page_Model_Mapper_Page();
		return $d;
	}
	
	

	public function rangAction()
	{
		$pages = $this->getModelMapperPage();
		$pagesOrder = $this->_getParam('page');
		$pid = intval($this->_getParam('pid', -1));
		$result = array();
		$result['result'] = true;
		$result['msg'] = array();
		foreach ($pagesOrder as $k => $t) {
			if ($t > 0) {
				$t = intval($t);
				$k = intval($k);
				$row = array('id' => $t, 'rang' => $k);
				if ($pid > -1) $row['pid'] = $pid;
				$p = $pages->createNew($row);
				if ($p) {
					$p->save();
				} else {
					$result['result'] = false;
					$result['msg'][] = 'ошибка при сохранении статьи id#'.$t;
				}
			}
		}
		if (sizeof($result) > 0) {
			$result['msg'] = implode(', ', $result['msg']);
		} else {
			unset($result['msg']);
		}
		$pages->getTree()->flushCache();
		$this->view->result = $result;
	}

	public function getFormEdit() {
		$form = new Ifrond_Module_Page_Form_Edit(array(
		'mapperPage' => $this->getModelMapperPage()
		));
		return $form;
	}

	public function getFormSeo() {
		$form = new Ifrond_Module_Page_Form_Seo();
		return $form;
	}

	public function editAction()
	{
		$id = intval($this->_getParam('id', 0));
		$this->view->form = $this->getFormEdit();
	}

	public function preparePageRow($page)
	{
		$pageRow = $page->getRow();
		$tags = array();
		if (isset($pageRow['tags']) && sizeof($pageRow['tags']) > 0) {
			foreach ($pageRow['tags'] as $t) {
				$tags[] = $t['title'];
			}
			$pageRow['tags'] = implode(', ', $tags);
		} else {
			$pageRow['tags'] = '';
		}
		$pageResult = array();
		foreach ($pageRow as $k => $t) {
			$k = 'fpe'.ucfirst(str_replace('_', '', $k));
			$pageResult[$k] = $t;
		}
		return $pageResult;
	}

	public function getpageAction()
	{
		$id = intval($this->_getParam('id', 0));
		$result = array();
		$result['result'] = true;
		if ($id > 0) {
			$pages = $this->getModelMapperPage();
			$page = $pages->find($id);
			$page->getTags();
			if ($page) {
				$result['page'] = $this->preparePageRow($page);
			} else {
				$result['result'] = false;
				$result['msg'] = 'Запрошенной страницы не существует';
			}
		} else {
			$result['result'] = false;
			$result['msg'] = 'Неверный формат запроса';
		}
		$this->view->page = $result;
	}

	public function deleteAction()
	{
		$id = intval($this->_getParam('id', 0));
		$result = array();
		$result['result'] = true;
		if ($id > 0) {
			$pages = $this->getModelMapperPage();
			$page = $pages->find($id);
			if ($page) {
				$page->delete();
			} else {
				$result['result'] = false;
				$result['msg'] = 'Запрошенной страницы не существует';
			}
		} else {
			$result['result'] = false;
			$result['msg'] = 'Неверный формат запроса';
		}
		$this->view->result = $result;
	}

	public function moveAction()
	{
		$id = intval($this->_getParam('id', 0));
		$pid = intval($this->_getParam('pid', -1));
		if ($pid > -1) {
				
		} else {
				
		}
		$result = array();
		$result['result'] = true;
		if ($id > 0) {
			$pages = $this->getModelMapperPage();
			$page = $pages->find($id);
			if ($page) {
				$page->delete();
			} else {
				$result['result'] = false;
				$result['msg'] = 'Запрошенной страницы не существует';
			}
		} else {
			$result['result'] = false;
			$result['msg'] = 'Неверный формат запроса';
		}
		$this->view->result = $result;
	}

	public function saveAction()
	{

		if (!$this->getRequest()->isPost()) {
			return $this->_forward('edit');
		}
		$form = $this->getFormEdit();
		$pages = $this->getModelMapperPage();
		$result = array();
		$result['result'] = true;
		if ($form->isValid($_POST)) {
			$values = $form->getValues();
			$row = array();
			$id = $values['fpeId'];
			$row['id'] = $id;
			$row['pid'] = $values['fpePid'];
			$row['is_active'] = 1;
			$row['is_cat'] = $values['fpeIscat'];
			$row['title'] = $values['fpeTitle'];
			$row['subtitle'] = $values['fpeSubtitle'];
			$row['text'] = $values['fpeText'];
			$row['author'] = $this->_user->id;
			$row['shortcut'] = $values['fpeShortcut'];
			$row['seo_title'] = $values['fpeSeotitle'];
			$row['seo_description'] = $values['fpeSeodescription'];
			$row['seo_keywords'] = $values['fpeSeokeywords'];
			if ($row['shortcut'] == '') {
				$row['shortcut'] = $row['title'];
			}
			$row['rang'] = $values['fpeRang'];
			if ($row['rang'] == -1) {
				$row['rang'] = $pages->getNextRang($row['pid']);
			}
			$page = $pages->createNew($row);
			$page->prepare();
			$page->save();
			$tags = $this->cleanTags($values['fpeTags']);
			$page->setTags($tags);			
			$pages->getTree()->flushCache();
			$result['page'] = $this->preparePageRow($page);
			if ($id == 0) $result['append'] = true;
			else $result['append'] = false;
			$this->view->page = $page;
		} else {
			$errors = $form->getMessages();
			$result['result'] = false;
			$result['errors'] = array();
			foreach ($errors as $k => $t) {
				if (sizeof($t) > 0) {
					$result['errors'][$k] = implode(', ', $t);
				}
			}
			$result['msg'] = implode(', ', $result['errors']);
		}
		$this->view->result = $result;
	}
	
	public function cleanTags($tagsStr) {
		$tagsList = explode(',', $tagsStr);
		foreach ($tagsList as $k => $t) {
			$tagsList[$k] = clearStr($t);
		}
		return $tagsList;
	} 

	public function seoAction()
	{
		$form = $this->getFormSeo();
		$this->view->form = $form;
	}


}