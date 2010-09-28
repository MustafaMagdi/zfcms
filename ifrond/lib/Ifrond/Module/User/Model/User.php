<?php
class Ifrond_Module_User_Model_User extends Ifrond_Model_Item
{

	function register($values) {		
		$values['date_register'] = date('Y-m-d H:i:s');
		$values['is_active'] = 0;
		$values['role'] = 'user';
		$values['register_code'] = md5($values['date_register'].'_'.$values['email']);
		$values['password'] = md5($values['password']);
		$this->setRow($values);
		$this->save();		
	}
	
	function checkUnique($values) {	
		$table = $this->getMapper()->getDbTable();
		$select = $table->select()
				->where('username = ?', $values['username'])
				->orWhere('email = ?', $values['email']);	
		$rows = $table->fetchAll($select);
		if (sizeof($rows) == 0) return true;
		else return false;			
	}

	function cofirm($code) {
		$table = $this->getMapper()->getDbTable();
		$select = $table->select()
				->where('register_code = ?', $code);	
		$rows = $table->fetchAll($select);
		if (sizeof($rows) == 0) return false;
		$this->setRow($rows[0]->toArray());		
		if ($this->is_active == 0) {
			$this->is_active = 1;
			$this->register_code = '';
			$this->save();			
		}
		return true;
	}

	function sendConfirmation($msg, $values) {		
		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml($mt);
		$mail->setFrom('robot@ulmap.ru', 'Ulmap Robot');
		$mail->addTo($values['email'], $values['username']);
		$mail->setSubject('Подтверждение регистрации на сайте КАРТА УЛЬЯНОВСКА');
		$mail->send();
	}


}