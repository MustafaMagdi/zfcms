<?php

class Ifrond_Module_News_Controller_Subscribe extends Ifrond_Controller_Action_Public
{
	public function getFormSubscribe() {
		$d = new Ifrond_Module_News_Form_Subscribe();
		return $d;
	}

	public function getModelMapperSubscribe() {
		$d = new Ifrond_Module_News_Model_Mapper_Subscribe();
		return $d;
	}

	public function indexAction()
	{
		$form = $this->getFormSubscribe();
		$this->view->form = $form;
	}

	public function sendConfirmMail($subscribe) {
		$this->view->subscribe = $subscribe;
		$letterBody = $this->view->render('subscribe/letter-sub.phtml');

		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml($letterBody);
		$mail->setFrom('newsletter@ulgov.ru', 'Рассылка Губернатора и Правительства Ульяновской области');
		$mail->addTo($subscribe->email, 'Подписчик');
		$mail->setSubject('Подтверждение подписки на рассылку');
		$mail->send();
		//_l($letterBody);
	}

	public function sendConfirmUnMail($subscribe) {
		$this->view->subscribe = $subscribe;
		$letterBody = $this->view->render('subscribe/letter-unsub.phtml');

		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml($letterBody);
		$mail->setFrom('newsletter@ulgov.ru', 'Рассылка Губернатора и Правительства Ульяновской области');
		$mail->addTo($subscribe->email, 'Подписчик');
		$mail->setSubject('Подтверждение удаления подписки');
		$mail->send();
		//_l($letterBody);
	}

	public function doAction()
	{
		$form = $this->getFormSubscribe();
		$this->view->form = $form;
		//_p($_POST);
		if ($form->isValid($_POST)) {
			$values = $form->getValues();
			$row = array();
			if ($values['act'] == 1) {
				$email = $values['email'];
				$subscribeMapper = $this->getModelMapperSubscribe();
				$rowsWithEmail = $subscribeMapper->getByEmail($email);
				$rowsNum = sizeof($rowsWithEmail);
				if ($rowsNum > 0) {
					$t = $rowsWithEmail[0];
					if ($t->is_active == 0) {
						$t->keycode = md5(uniqid(rand(), true));
						$t->save();
						$this->sendConfirmMail($t);
						$this->_helper->viewRenderer('resend');
					} else {
						$this->_helper->viewRenderer('nounique');
					}
				} else {
					$row['email'] = $email;
					$row['is_active'] = 0;
					$now = date('Y-m-d H:i:s');
					$row['date_add'] = $now;
					$row['date_edit'] = $now;
					$row['keycode'] = md5(uniqid(rand(), true));
					$subscribe = $subscribeMapper->createNew($row);
					$this->view->subscribe = $subscribe;
					$subscribe->save();
					$this->sendConfirmMail($subscribe);
				}
			}
			if ($values['act'] == 2) {
				$this->_forward('unsubscribe', null, null, array(
					'email' =>  $values['email']
				));
			}
		} else {
			$this->_helper->viewRenderer('index');
		}

	}

	public function unsubscribeAction()
	{
		$email = clearStr($this->_getParam('email', ''));
		$subscribeMapper = $this->getModelMapperSubscribe();
		$rowsWithEmail = $subscribeMapper->getByEmail($email);
		$rowsNum = sizeof($rowsWithEmail);
		if ($rowsNum > 0) {
			$t = $rowsWithEmail[0];
			$now = date('Y-m-d H:i:s');
			$row['date_edit'] = $now;
			$t->keycode = md5(uniqid(rand(), true));
			$t->save();
			$this->sendConfirmUnMail($t);
		}
	}

	public function confirmAction()
	{
		$subscribeMapper = $this->getModelMapperSubscribe();
		$k = $this->_getParam('k', false);
		$action = $this->_getParam('a', 'add');
		if ($k == false) {
			$this->_forward('index');
		} else {
			$kParts = explode('_', $k);
			if (sizeof($kParts) != 2) {
				$this->_helper->viewRenderer('wrongkey');
			} else {
				$id = intval($kParts[0]);
				$key = clearStr($kParts[1]);
				if ($id == 0 || strlen($key) != 32) {
					$this->_helper->viewRenderer('wrongkey');
				} else {
					$subscribe = $subscribeMapper->getByKey($key, $id);
					if ($subscribe == false) {
						$this->_helper->viewRenderer('wrongkey');
					} else {
						if ($action == 'un') {
							$subscribe->delete();
							$this->_helper->viewRenderer('confirm-un');
						} else {
							$subscribe->is_active = 1;
							$subscribe->date_edit = date('Y-m-d H:i:s');
							$subscribe->save();
						}
						$this->view->subscribe = $subscribe;
					}
				}
			}
		}
	}




}