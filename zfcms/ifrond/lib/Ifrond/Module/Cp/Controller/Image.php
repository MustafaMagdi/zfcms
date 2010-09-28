<?php

class Ifrond_Module_Cp_Controller_Image extends Ifrond_Controller_Action_Admin
{

	protected $_imageDir;
	protected $_parentModuleName;
	protected $_pathPrefix;
	protected $_allowedExtension = array('jpg','png','gif','jpeg');
	//protected $_maxSize = 6291;
	protected $_maxSize = 3291456;


	public function getModelImagedir() {
		$d = new Ifrond_Module_Cp_Model_Imagedir();
		return $d;
	}
	
	public function getModelThumb($path) {
		$d = new Ifrond_Model_Thumb($path);
		return $d;
	}
	
	public function init() {
		parent::init();
		$this->_pathPrefix = PATH_PUB.'/images/atts';
		$path = $this->_pathPrefix;
		$d = $this->getModelImagedir();
		$d->setRequiredExtentions($this->_allowedExtension);
		$this->_parentModuleName = $this->_getParam('pmodule', '');
		$subDirPath = $this->_getParam('subdir', '');
		$dirPath = $this->_getParam('dir', '');
		if ($dirPath != '') {
			$path = $path.'/'.$dirPath;
		} else {
			if ($this->_parentModuleName != '') {
				$path = $path.'/'.$this->_parentModuleName;
			}
			if ($subDirPath != '') {
				$path = $path.'/'.$subDirPath;
			}

		}
		$d->setDir($path);
		$this->_imageDir = $d;
	}

	public function getFormUploadOptions() {
		$dir = $this->_imageDir->getDirPath();
		$row = array(
			'pmodule' => $this->_parentModuleName,
			'fileDestination' => $dir,		
			'fileValidators' => array(
				'Count' => 1,
				'Size' => $this->_maxSize,
				'Extension' => implode(',', $this->_allowedExtension),
				'ExcludeExtension' => '')
		);
		return $row;
	}

	public function getFormUpload() {
		$options = $this->getFormUploadOptions();
		$form = new Cp_Form_Fileupload($options);
		return $form;
	}

	public function indexAction() {
		$images = $this->_imageDir->getFiles();
		foreach ($images as $k => $t) {
			$thumb = $this->getModelThumb($t['path']);
			$images[$k]['thumb'] = $thumb->adaptiveResize(80, 80)->saveQuick();
		}
		$this->view->form = $this->getFormUpload();
		$this->view->images = $images;
		$this->view->pmodule = $this->_parentModuleName;
		$this->view->dir = str_replace(PATH_PUB, '', $this->_imageDir->getDirPath());
		unset($thumb);
	}

	public function uploadAction() {
		$this->_helper->layout()->disableLayout();
		$result = array();
		$result['result'] = 'false';
		$result['msg'] = '';
		$result['html'] = '';
		if ($this->getRequest()->isPost()) {
			$form = $this->getFormUpload();
			$postData = $this->getRequest()->getPost();
			$valid = $form->isValid($postData);

			if (!$valid) {
				$result['result'] = 'false';
				$result['msg'] = implode(', ', $form->getMessages('fcfFile'));
			} else {
				$form->checkFilename($_POST);
				if (!$form->fcfFile->receive()) {
					$result['result'] = 'false';
					$result['msg'] = 'Ошибки при загрузке файла';
				}
				$newfile = $form->fcfFile->getFileName();
				$thumb = $this->getModelThumb($newfile);
				$thumb->adaptiveResize(80, 80)->saveQuick();
				//_p($thumb);
				$alt = str_replace(PATH_WEBROOT, '', $thumb->getFileName());
				$alt = str_replace('\\', '/', $alt);
				$result['result'] = 'true';
				$img = $thumb->getImageAsTag(array('alt' => $alt));
				$result['html'] = '<li id="'.basename($alt).'" rel="'.$this->_parentModuleName.'">'.$img.'</li>';
			}
			$this->view->result = $result;
		}
	}

	public function deleteAction() {
		$result = array();
		$result['result'] = false;
		$result['msg'] = '';
		$result['html'] = '';
		$filename = $this->_getParam('filename', '');
		$dir = $this->_imageDir->getDirPath();
		$file = $dir.'/'.$filename;
		if (!is_dir($file) && is_file($file)) {
			$fileparts = pathinfo($file);
			if (in_array($fileparts['extension'], $this->_allowedExtension)) {
				if (unlink($file)) {
					$result['result'] = true;
					$result['id'] = str_replace('.', '\\.', basename($file));
				}
			} else {
				$result['msg'] = 'Вы не имеете права удалять этот файл';
			}
		} else {
			$result['msg'] = 'Указанного файла не существует';
		}
		$this->view->result = $result;
	}


}

