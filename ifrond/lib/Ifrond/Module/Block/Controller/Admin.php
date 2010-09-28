<?php

class Ifrond_Module_Block_Controller_Admin extends Ifrond_Controller_Action_Admin
{
	protected $_allowedExtension = array('jpg','png','gif','jpeg');
	protected $_maxSize = 6291456;
	protected $_refDir = '/images/atts/block';

	public function getModelMapperBlock() {
		$d = new Ifrond_Module_Block_Model_Mapper_Block();
		return $d;
	}

	public function getFormEdit($id, $template) {
		$row = array(
			'fields' => $template,
			'id' => $id,
			'fileDestination' => PATH_PUB.$this->_refDir,
        	'refTemplate' => $template,
			'fileValidators' => array(
				'Count' => 1,
				'Size' => $this->_maxSize,
				'Extension' => implode(',', $this->_allowedExtension),
				'ExcludeExtension' => '')
		);
		$d = new Ifrond_Module_Block_Form_Edit($row);
		return $d;
	}

	public function saveAction() {
		$this->_helper->layout()->disableLayout();
		$id = intval($this->_getParam('id', 0));
		$result = array();
		$result['result'] = 'false';
		$result['msg'] = '';
		$result['html'] = '';
		if ($id > 0) {
			$result['id'] = $id;
			$blockMapper = $this->getModelMapperBlock();
			$block = $blockMapper->find($id);
			$fields = simplexml_load_string($block->template);
				
			if ($this->getRequest()->isPost()) {
				$files = array();
				$postData = $this->getRequest()->getPost();
				$form = $this->getFormEdit($id, $fields);
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
					$values = $form->getValues();
					if (sizeof($_FILES) > 0) {
						foreach ($_FILES as $k => $value) {
							if ($form->$k->receive()) {
								if ($form->checkFilename($k)) {
									$newfile = $form->$k->getFileName();
									$url = str_replace(PATH_WEBROOT, '', $newfile);
									$url = str_replace('\\', '/', $url);
									$files[$k] = $url;
								}
							} else {
								$result['result'] = 'false';
								$result['msg'] = 'Ошибки при загрузке файла';
								$this->view->result = $result;
								return true;
							}
						}
					}
						
					if (sizeof($files) > 0) {
						foreach ($files as $k => $t) {
							$values[$k] = $t;
						}
					}
					$fields = simplexml_load_string($block->template);
					$actualValues = unserialize($block->value);
					foreach ($fields->children() as $t) {
						$key = $t->type;
						$name = $t->getName();
						if ($key == 'image' or $key == 'file') {
							if (isset($files[$name])) {
								$actualValues[$name] = $files[$name];
							}
						} else {
							$actualValues[$name] = $values[$name];
						}
						
					}
					$block->value = serialize($actualValues);
					$block->save();
					$result['result'] = 'true';
				}
			}
		}		
		$this->view->result = $result;
	}

	public function editAction() {
		$id = intval($this->_getParam('id', 0));
		if ($id > 0) {
			$blockMapper = $this->getModelMapperBlock();
			$block = $blockMapper->find($id);
			$fields = simplexml_load_string($block->template);
			$values = unserialize($block->value);
			if (is_array($values) && sizeof($values) > 0) {				
				foreach ($values as $k => $t) {
					$fields->$k->value = $t;
				}
			}
			$form = $this->getFormEdit($id, $fields);
			$this->view->form = $form;
			$this->view->block = $block;

		}
	}



	public function indexAction() {
		$blockMapper = $this->getModelMapperBlock();
		$this->view->blocks = $blockMapper->getAll();
	}


}