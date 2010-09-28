<?php

class Ifrond_Module_Cp_Controller_File extends Ifrond_Controller_Action_Admin
{

	protected $_fileDir;
	protected $_parentModuleName;
	protected $_pathPrefix;
	protected $_allowedExtension = array();
	protected $_disallowedExtension = array('exe', 'cmd', 'bat', 'lnk',
			'php', 'php3', 'php4', 'php5', 'php6', 'pl',
			'sh', 'bin', 'htaccess', 'cgi', 'asp', 'rb', 'ruby', 'py', 'install');
	protected $_maxSize = 3291456;


	public function getModelFiledir() {
		$d = new Ifrond_Module_Cp_Model_Filedir();
		return $d;
	}
	
	public function init() {
		parent::init();
		$this->_pathPrefix = PATH_PUB.'/atts';
		$path = $this->_pathPrefix;
		$d = $this->getModelFiledir();
		$d->setDisallowedExtentions($this->_disallowedExtension);
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
		$this->_fileDir = $d;
	}

	public function getFormUploadOptions() {
		$dir = $this->_fileDir->getDirPath();
		$row = array(
			'pmodule' => $this->_parentModuleName,
			'fileDestination' => $dir,	
			'attType' => 'file',	
			'fileValidators' => array(
				'Count' => 1,
				'Size' => $this->_maxSize,
				'Extension' => '',
				'ExcludeExtension' => implode(',', $this->_disallowedExtension))
		);
		return $row;
	}

	public function getFormUpload() {
		$options = $this->getFormUploadOptions();
		$form = new Cp_Form_Fileupload($options);
		return $form;
	}

	public function indexAction() {
		$files = $this->_fileDir->getFiles();
		$this->view->form = $this->getFormUpload();
		$this->view->files = $files;
		$this->view->pmodule = $this->_parentModuleName;
		$this->view->dir = str_replace(PATH_PUB, '', $this->_fileDir->getDirPath());
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
				$result['result'] = 'true';
				$alt = str_replace(PATH_WEBROOT, '', $newfile);
				$alt = str_replace('\\', '/', $alt);
				$result['html'] = '<li id="'.basename($newfile).'" rel="'.$this->_parentModuleName.'">'
				.basename($newfile)
				.' <a href="'.$alt.'" target="_blank" title="скачать">&nbsp;</a>'
				.'</li>';
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
		$dir = $this->_fileDir->getDirPath();
		$file = $dir.'/'.$filename;
		if (!is_dir($file) && is_file($file)) {
			$fileparts = pathinfo($file);
			if (!in_array($fileparts['extension'], $this->_disallowedExtension)) {
				if (unlink($file)) {
					//_p(realpath($file).'||'.$file);
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

