<?php

class Ifrond_Module_Feedback_Controller_Index extends Ifrond_Controller_Action_Public
{

	protected $_dir = '/atts/feedback';
	protected $_maxSize = 3291456;
	protected $_allowedExtension = array('rtf', 'doc', 'docx', 'rtf', 'odf', 'txt', 
										 'mp3', 'avi', 'jpeg', 'jpg', 'png', 'gif',
										 'zip', 'rar', 'gz', 'tgz', 'xls', 'xlsx',
										 'mp4', 'mov'
										);

	public function getFormFeedback() {
		//_p(PATH_PUB.$this->_dir);
		$row = array(
			'fileDestination' => PATH_PUB.$this->_dir,
			'fileValidators' => array(
				'Count' => 1,
				'Size' => $this->_maxSize,
				'Extension' => implode(',', $this->_allowedExtension),
				'ExcludeExtension' => '')
		);
		//_p($row);
		$d = new Ifrond_Module_Feedback_Form_Feedback($row);
		return $d;
	}

	public function indexAction() {
		$form = $this->getFormFeedback();
		$this->view->form = $form;
	}

	public function receptionAction() {
		if ($_POST['agree'] == 1) {
			$form = $this->getFormFeedback();
			$this->view->form = $form;
		} else {
			$this->_helper->viewRenderer('no');
		}
	}

	public function sendAction() {
		if ($this->getRequest()->isPost()) {
			$postData = $this->getRequest()->getPost();
			$form = $this->getFormFeedback();
			$valid = $form->isValid($postData);
			if (!$valid) {
				$result['result'] = 'false';
				$errors = $form->getMessages();
				$this->view->form = $form;
				$this->_helper->viewRenderer('reception');
			} else {
				$this->view->form = false;				
				$files = array();
				if (sizeof($_FILES) > 0) {
					foreach ($_FILES as $k => $value) {
						//$form->$k->receive();
						//_p($value['name'], false);
						if ($value['name'] != '') {
							$form->checkFilename($k);
							if ($form->$k->receive()) {
								//if ($form->checkFilename($k)) {
								$newfile = $form->$k->getFileName();
								$url = str_replace(PATH_WEBROOT, '', $newfile);
								$url = str_replace('\\', '/', $url);
								$files[$k] = $url;
								//}
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
				}
				$values = $form->getValues();
				$row = array();
				$values['person_occupation'] = $form->getOptionTitle('occupation', $values['person_occupation']);
				$values['person_privilege'] = $form->getOptionTitle('privilege', $values['person_privilege']);
				$values['address_region'] = $form->getOptionTitle('region', $values['address_region']);
				$values['address_district'] = $form->getOptionTitle('district', $values['address_district']);
				$values['power_federal'] = $form->getOptionTitle('federal', $values['power_federal']);
				$values['power_local'] = $form->getOptionTitle('local', $values['power_local']);
				$values['msg_topic'] = $form->getOptionTitle('topic', $values['msg_topic']);
				$this->view->values = $values;
				$this->view->files = $files;				
				$letterBody = $this->view->render('index/letter.phtml');				
				$mail = new Zend_Mail('UTF-8');			
				$mail->setBodyHtml($letterBody);
				$mail->setFrom('robot@ulgov.ru', 'UlgovRobot');
				//$mail->addTo('priem@ulgov.ru', 'Отдел по работе с обращениями граждан');
				$mail->addTo('info@ifrond.com', 'Равиль');
				$mail->setSubject('обращение в виртуальной приемной');
				$mail->send();
				


			}
		}
	}


}