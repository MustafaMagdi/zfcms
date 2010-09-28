<?php
class Ifrond_Module_Ref_Form_Edit extends Zend_Form
{

	protected $_refTemplate = array();
	protected $_fileDestination = '';
	protected $_fileValidators = array(
		'Count' => 1,
		'Size' => 102400,
		'Extension' => 'jpg,png,gif,jpeg',
		'ExcludeExtension' => ''
	);

	public function setRefTemplate($template) {
		$this->_refTemplate = $template;
	}
	
	public function setFileDestination($v) {
		$this->_fileDestination = $v;
	} 	
	
	public function checkFilename() {	
		$fileName = $this->freThumb->getFileName();		
		if (is_array($fileName) && sizeof($fileName) == 0) return false;
		$oldname = pathinfo($fileName);
		$pattern = '/[^a-zA-Z0-9_-]/';
  		$oldname['filename'] = preg_replace($pattern, '', $oldname['filename']);
  		if ($oldname['filename'] == '') $oldname['filename'] = date('Ymd');
		$filename = $this->_fileDestination.'/'.$oldname['filename'].'.'.$oldname['extension'];
		$i = 1;
		while (is_file($filename)) {
			$filename = $this->_fileDestination.'/'.$oldname['filename'].'_'.$i.'.'.$oldname['extension'];
			$i++;
		}
		$this->freThumb->addFilter('Rename', $filename);
		return true;		
	}

	public function init()
	{
		$this->setAction('/ref/admin/save/')
		->setMethod('post')
		->setAttrib('id', 'Ref_Form_Edit');
		
		$this->setAttrib('enctype', 'multipart/form-data');

		$this->addElement('text', 'freTitle', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => '',
            'label'      => 'Название',		
			'attribs'  => array('class' => 'texter', 'style' => 'width:93%;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));

		if ($this->_refTemplate['subtitle'] == 1) {
			$this->addElement('textarea', 'freSubtitle', array(
	            'filters'    => array('StripTags', 'StringTrim'),
	            'validators' => array(array('StringLength', false, array(3, 500))),
	            'required'   => false,
	            'label'      => 'Подзаголовок',
				'attribs'  => array('class' => 'texter', 'style' => 'width:93%;height:57px;'),
				'decorators' => array( 'ViewHelper', 'Errors' )
			));
		}
		
		if ($this->_refTemplate['thumb'] == 1) {
			$element = new Zend_Form_Element_File('freThumb');
			$element->setLabel('Загрузить')->setDestination($this->_fileDestination);	
				
			$element->addValidator('Count', false, $this->_fileValidators['Count']);
			$element->addValidator('Size', false, $this->_fileValidators['Size']);	
			if ($this->_fileValidators['Extension'] != '') {	
				$element->addValidator('Extension', false, $this->_fileValidators['Extension']);			
			}
			if ($this->_fileValidators['ExcludeExtension'] != '') {	
				$element->addValidator('ExcludeExtension', false, $this->_fileValidators['ExcludeExtension']);
			}
			$element->setMaxFileSize($this->_fileValidators['Size']);
			$element->setRequired(false);
			
			$element->setDecorators(array('File', 'Description', 'Errors'));
			$this->addElement($element);
		}
		
		if ($this->_refTemplate['extra1'] != '') {
			$this->addElement('text', 'freExtra1', array(
	            'filters'    => array('StripTags', 'StringTrim'),
	            'validators' => array(array('StringLength', false, array(3, 900))),
	            'required'   => false,
				'value' => '',
	            'label'      => $this->_refTemplate['extra1'],	
				'attribs'  => array('class' => 'texter', 'style' => 'width:93%;'),			
				'decorators' => array( 'ViewHelper', 'Errors' )
			));
		}
		
		if ($this->_refTemplate['extra2'] != '') {
			$this->addElement('text', 'freExtra2', array(
	            'filters'    => array('StripTags', 'StringTrim'),
	            'validators' => array(array('StringLength', false, array(3, 900))),
	            'required'   => false,
				'value' => '',
	            'label'      => $this->_refTemplate['extra2'],	
				'attribs'  => array('class' => 'texter', 'style' => 'width:93%;'),		
				'decorators' => array( 'ViewHelper', 'Errors' )
			));
		}
		
		if ($this->_refTemplate['extra3'] != '') {
			$this->addElement('text', 'freExtra3', array(
	            'filters'    => array('StripTags', 'StringTrim'),
	            'validators' => array(array('StringLength', false, array(3, 900))),
	            'required'   => false,
				'value' => '',
	            'label'      => $this->_refTemplate['extra3'],		
				'attribs'  => array('class' => 'texter', 'style' => 'width:93%;'),	
				'decorators' => array( 'ViewHelper', 'Errors' )
			));
		}
		
		if ($this->_refTemplate['extra4'] != '') {
			$this->addElement('text', 'freExtra4', array(
	            'filters'    => array('StripTags', 'StringTrim'),
	            'validators' => array(array('StringLength', false, array(3, 900))),
	            'required'   => false,
				'value' => '',
	            'label'      => $this->_refTemplate['extra4'],	
				'attribs'  => array('class' => 'texter', 'style' => 'width:93%;'),		
				'decorators' => array( 'ViewHelper', 'Errors' )
			));
		}		

		$this->addElement('hidden', 'freId', array(
			'value' => 0,
			'filters'    => array('Int'), 
			'required'   => true,
			'decorators' => array( 'ViewHelper', 'Errors' )));

		$this->addElement('hidden', 'frePid', array(
			'value' => $this->_refTemplate['id'],
			'filters'    => array('Int'), 
			'required'   => true,
			'decorators' => array( 'ViewHelper', 'Errors' )));	

		$this->addElement('hidden', 'freRang', array(
			'value' => -1,
			'filters'    => array('Int'), 
			'required'   => true,
			'decorators' => array( 'ViewHelper', 'Errors' )));
		
		
		
		$save = $this->addElement('submit', 'fpeSave', array(
            'required' => false,            
            'label'    => 'Сохранить',
			'decorators' => array( 'ViewHelper', 'Errors' )		
		));

		$this->setDecorators(array(array('viewScript', array('viewScript' => '/admin/forms/edit.phtml'))));




	}
}