<?php
class Ifrond_Module_Cp_Form_Fileupload extends Zend_Form
{
	protected $_pmodule = '';
	protected $_attType = 'image';
	protected $_fileDestination = '';
	protected $_fileValidators = array(
		'Count' => 1,
		'Size' => 102400,
		'Extension' => 'jpg,png,gif,jpeg',
		'ExcludeExtension' => ''
	);


	public function setFileDestination($v) {
		$this->_fileDestination = $v;
	}

	public function setPmodule($v) {
		$this->_pmodule = $v;
	}

	public function setFileValidators($v) {
		$this->_fileValidators = $v;
	}
	
	public function setAttType($v) {
		$this->_attType = $v;
	}

	public function checkFilename($data) {
		$oldname = pathinfo($this->fcfFile->getFileName());
//		$pattern = '/[^a-zA-Z0-9_.-\/:]/';
		$pattern = '/[^a-zA-Z0-9_-]/';
  		$oldname['filename'] = preg_replace($pattern, '', $oldname['filename']);
  		if ($oldname['filename'] == '') $oldname['filename'] = date('Ymd');
		$filename = $this->_fileDestination.'/'.$oldname['filename'].'.'.$oldname['extension'];
		$i = 1;
		while (is_file($filename)) {
			$filename = $this->_fileDestination.'/'.$oldname['filename'].'_'.$i.'.'.$oldname['extension'];
			$i++;
		}
		$this->fcfFile->addFilter('Rename', $filename);		
	}

	public function init()
	{
		if ($this->_pmodule != '') $pmodule = 'pmodule/'.$this->_pmodule.'/';
		else $pmodule = '';
		
		$this->setAction('/cp/'.$this->_attType.'/upload/'.$pmodule);
		$this->setAttrib('id', 'cpFileUpload');
		$this->setAttrib('enctype', 'multipart/form-data');
		
		$element = new Zend_Form_Element_File('fcfFile');
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
		$element->addValidator('NotEmpty');		
		$element->setRequired(true)/*->setValueDisabled(true)*/;
		
		$element->setDecorators(array('File', 'Description', 'Errors'));
		$this->addElement($element);
		
		$dir = str_replace(PATH_PUB, '', $this->_fileDestination);
		$this->addElement('hidden', 'fcfDir', array(
			'value' => $dir,
			'filters'    => array('StripTags', 'StringTrim'),
			'required'   => false,
			'decorators' => array( 'ViewHelper', 'Errors' )));

		$save = $this->addElement('submit', 'fcfUpload', array(
            'required' => false,            
            'label'    => '>>',
			'decorators' => array( 'ViewHelper', 'Errors' )		
		));


		$this->setDecorators(array(
		array('viewScript', array('viewScript' => 'forms/file.phtml'))
		));
	}
}