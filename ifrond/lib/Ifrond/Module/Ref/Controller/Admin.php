<?php

class Ifrond_Module_Ref_Controller_Admin extends Ifrond_Controller_Action_Admin
{

	protected $_allowedExtension = array('jpg','png','gif','jpeg');
	protected $_maxSize = 3291456;
	protected $_refDir = '/images/atts/ref';


	public function getModelMapperRef() {
		$d = new Ifrond_Module_Ref_Model_Mapper_Ref();
		return $d;
	}

	public function getModelMapperRefTemplate() {
		$d = new Ifrond_Module_Ref_Model_Mapper_RefTemplate();
		return $d;
	}

	public function getFormEdit($template = false)
	{
		$row = array(
			'fileDestination' => PATH_PUB.$this->_refDir,
        	'refTemplate' => $template,
			'fileValidators' => array(
				'Count' => 1,
				'Size' => $this->_maxSize,
				'Extension' => implode(',', $this->_allowedExtension),
				'ExcludeExtension' => '')
		);
		$form = new Ifrond_Module_Ref_Form_Edit($row);
		return $form;
	}

	public function getModelThumb($path) {
		$d = new Ifrond_Model_Thumb($path);
		return $d;
	}

	public function saveAction() {
		$this->_helper->layout()->disableLayout();
		$result = array();
		$result['result'] = 'false';
		$result['msg'] = '';
		$result['html'] = '';
		if ($this->getRequest()->isPost()) {
			$pid = intval($this->_getParam('frePid', 0));
			$refTemplate = $this->getModelMapperRefTemplate();
			$template = $refTemplate->find($pid);
			$templateRow = $template->getRow();
			$form = $this->getFormEdit($templateRow);
			$postData = $this->getRequest()->getPost();
			$valid = $form->isValid($postData);
			if (!$valid) {
				$result['result'] = 'false';
				$errors = $form->getMessages();
				$msgs = array();
				foreach ($errors as $t) {
					$msgs[] = implode(',', $t);
				}				
				$result['msg'] = implode(', ', $msgs);
			} else {
				$row = array();
				if ($templateRow['thumb'] == 1) {
					if ($form->freThumb->receive()) {
						if ($form->checkFilename()) {
							$newfile = $form->freThumb->getFileName();							
							$url = str_replace(PATH_WEBROOT, '', $newfile);
							$url = str_replace('\\', '/', $url);
							$row['thumb'] = $url;		
						}							
					} else {
						$result['result'] = 'false';
						$result['msg'] = 'Ошибки при загрузке файла';
						$this->view->result = $result;
						return true;
					}
					
				}
				$values = $form->getValues();				
				$id = $values['freId'];
				$row['id'] = $id;
				$row['pid'] = $values['frePid'];
				$row['rang'] = $values['freRang'];
				$row['title'] = $values['freTitle'];
				if ($templateRow['subtitle'] == 1) $row['subtitle'] = $values['freSubtitle'];
				if ($templateRow['extra1'] != '') $row['extra1'] = $values['freExtra1'];
				if ($templateRow['extra2'] != '') $row['extra2'] = $values['freExtra2'];
				if ($templateRow['extra3'] != '') $row['extra3'] = $values['freExtra3'];
				if ($templateRow['extra4'] != '') $row['extra4'] = $values['freExtra4'];
				$ref = $this->getModelMapperRef();
				if ($row['rang'] == -1) {
					$row['rang'] = $ref->getNextRang($row['pid']);
				}	
				$refOne = $ref->createNew($row);			
				$refOne->save();
				$refOne->update();
				$this->view->ref = $refOne;
				$result['result'] = 'true';
			}
			$this->view->result = $result;			
			$this->view->template = $template;
		}
	}

	public function rangAction()
	{
		$result = array();
		$result['result'] = 'false';
		$result['msg'] = '';
		
		$refs = $this->getModelMapperRef();
		$refsOrder = $this->_getParam('ref');
		$pid = intval($this->_getParam('pid', -1));
		
		foreach ($refsOrder as $k => $t) {
			if ($t > 0) {
				$t = intval($t);
				$k = intval($k);
				$row = array('id' => $t, 'rang' => $k);
				if ($pid > -1) $row['pid'] = $pid;
				$p = $refs->createNew($row);
				if ($p) {
					$p->save();
				} else {
					$result['result'] = false;
					$result['msg'][] = 'ошибка при сохранении справочника id#'.$t;
				}
			}
		}
		if (sizeof($result) > 0) {
			$result['msg'] = implode(', ', $result['msg']);
		} else {
			unset($result['msg']);
			$result['result'] = 'true';
		}		
		$this->view->result = $result;	
	}
	
	public function deleteAction()
	{
		$this->_helper->layout()->disableLayout();
		$result = array();
		$result['result'] = 'false';
		$result['msg'] = '';		
		$id = intval($this->_getParam('id', 0));
		$result['id'] = $id;
		if ($id > 0) {
			$refs = $this->getModelMapperRef();
			$ref = $refs->find($id);
			if ($ref->id > 0) {
				$ref->delete();
				$result['id'] = $ref->id;
			} else {
				$result['msg'] = 'указанный справочник не существует';
			}
		} else {
			$result['msg'] = 'укажите справочник';
		}
		$this->view->result = $result;	
	}

	public function indexAction()
	{
		$refTemplate = $this->getModelMapperRefTemplate();
		$this->view->templates = $refTemplate->getAll();
	}

	public function listAction()
	{
		$ref = $this->getModelMapperRef();
		$pid = intval($this->_getParam('pid', 0));
		$refList = $ref->getAllByPid(array('value' => $pid, 'order' => 'rang ASC'));
		$refTemplate = $this->getModelMapperRefTemplate();
		$template = $refTemplate->find($pid);
		$this->view->refs = $refList;
		$this->view->pid = $pid;
		$this->view->template = $template;
		$this->view->form = $this->getFormEdit($template->getRow());
	}
	
	public function getoneAction()
	{
		$ref = $this->getModelMapperRef();
		$id = intval($this->_getParam('id', 0));
		$refOne = $ref->find($id);
		$this->view->result = $refOne->getRow();
	}

	
	
	

}