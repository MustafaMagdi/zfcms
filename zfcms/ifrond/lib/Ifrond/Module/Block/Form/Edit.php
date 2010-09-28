<?php
class Ifrond_Module_Block_Form_Edit extends Zend_Form
{	
	
	protected $_fields;
	protected $_id;
	protected $_fileDestination = '';
	protected $_fileValidators = array(
		'Count' => 1,
		'Size' => 6291456,
		'Extension' => 'jpg,png,gif,jpeg',
		'ExcludeExtension' => ''
	);

	public function setFields($fields) {	
		$this->_fields = $fields;
	}
	
	public function setId($id) {	
		$this->_id = $id;
	}
	
	public function setFileDestination($v) {
		$this->_fileDestination = $v;
	} 
	
	public function setFileValidators($v) {
		$this->_fileValidators = $v;
	}	
	
	public function checkFilename($uploadname) {
		$k = (string) $uploadname;	
		$fileName = $this->$k->getFileName();		
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
		$this->$k->addFilter('Rename', $filename);
		return true;		
	}
	
	public function setText($field) {		
		$this->addElement('text', $field->getName(), array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => isset($field->required),
			'value' => $field->value,
			'description' => $field->desc,
            'label'      =>  $field->title,		
			'attribs'  => array('class' => 'texter', 'style' => 'width:93%;')
		));		
	}
	
	public function setTextarea($field) {		
		$this->addElement('textarea', $field->getName(), array(
            'filters'    => array('StringTrim'),            
            'required'   => isset($field->required),
			'description' => $field->desc,
			'value' => $field->value,
            'label'      =>  $field->title,		
			'attribs'  => array('class' => 'texter', 'style' => 'width:93%;height:100px;')
		));
	}
	public function setWysiwyg($field) {		
		$this->addElement('textarea', $field->getName(), array(
            'filters'    => array('StringTrim'),            
            'required'   => isset($field->required),
			'value' => $field->value,
			'description' => $field->desc,
            'label'      =>  $field->title,
			'attribs'  => array('class' => 'wysiwyg', 'style' => 'width:93%;height:300px;')
		));
	}
	public function setImage($field) {		
		$element = new Zend_Form_Element_File($field->getName());
		//$element->setDisableLoadDefaultDecorators(true);
		$element->setLabel($field->title);
		$element->setDestination($this->_fileDestination);				
		$element->addValidator('Count', false, $this->_fileValidators['Count']);
		$element->addValidator('Size', false, $this->_fileValidators['Size']);	
		if ($this->_fileValidators['Extension'] != '') {	
			$element->addValidator('Extension', false, $this->_fileValidators['Extension']);			
		}
		if ($this->_fileValidators['ExcludeExtension'] != '') {	
			$element->addValidator('ExcludeExtension', false, $this->_fileValidators['ExcludeExtension']);
		}
		$element->setMaxFileSize($this->_fileValidators['Size']);
		$element->setRequired(isset($field->required));
		$element->setDescription($field->desc);
		$element->setAttrib('preview', $field->value);
		
		$this->addElement($element, $field->getName());
		
		$imageInput = $this->getElement($field->getName()); 
        $imageInput->setDecorators(
        	array('File', 
        			array('ViewScript', array(
	        			'viewScript' => 'admin/forms/fileelement.phtml',
        				'placement' => false,
	    			)
	    		)
	    	)
	    );
		$this->setAttrib('enctype', 'multipart/form-data');		
	}
	
	public function init()
	{
		$this->setAction('/block/admin/save/')
		->setMethod('post')
		->setAttrib('id', 'Block_Form_Edit');
		
		$fields = $this->_fields;
		
		foreach ($fields->children() as $t) {			
			$key = $t->type;
			$normalized = ucfirst($key);
            $method = 'set' . $normalized;
            //_p($t);
            if (method_exists($this, $method)) {
                $this->$method($t);
            }			
		}		
		$this->addElement('hidden', 'id', array(
			'value' => $this->_id,
			'filters'    => array('StripTags', 'StringTrim'),
			'required'   => false,
			'decorators' => array( 'ViewHelper', 'Errors' )));
		
		$this->addElement('submit', 'save', array(
            'required' => false,            
            'label'    => 'Сохранить',				
			'decorators' => array( 'ViewHelper', 'Errors' )		
		));

		




	}
}