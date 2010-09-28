<?php
class Ifrond_Module_News_Form_Fileupload extends Ifrond_Module_Cp_Form_Fileupload
{
	protected $_fileValidators = array(
		'Count' => 1,
		'Size' => 502400,
		'Extension' => 'jpg,png,gif,jpeg',
		'ExcludeExtension' => ''
		);

		public function checkFilename($data) {
			$oldname = pathinfo($this->fnfFile->getFileName());
			$pattern = '/[^a-zA-Z0-9_-]/';
			$oldname['filename'] = preg_replace($pattern, '', $oldname['filename']);
			if ($oldname['filename'] == '') $oldname['filename'] = date('Ymd');
			$filename = $this->_fileDestination.'/'.$oldname['filename'].'.'.$oldname['extension'];
			$i = 1;
			while (is_file($filename)) {
				$filename = $this->_fileDestination.'/'.$oldname['filename'].'_'.$i.'.'.$oldname['extension'];
				$i++;
			}
			$this->fnfFile->addFilter('Rename', $filename);
		}

		public function init()
		{			
			$this->setAction('/news/admin/gallery/');
			$this->setAttrib('id', 'News_Form_Gallery');
			$this->setAttrib('enctype', 'multipart/form-data');

			$element = new Zend_Form_Element_File('fnfFile');
			$element->setLabel('Фото')->setDestination($this->_fileDestination);

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
			$element->setRequired(true);
			$element->setAttribs(array('class' => 'texter'));

			$element->setDecorators(array('File', 'Description', 'Errors'));
			$this->addElement($element);

			$this->addElement('hidden', 'fnfPid', array(
			'value' => -1,
			'filters'    => array('Int'),
			'required'   => false,			
			'decorators' => array( 'ViewHelper', 'Errors' )));
			
			$this->addElement('text', 'fnfTitle', array(
			'value' => '',
			'filters'    => array('StripTags', 'StringTrim'),
			'required'   => false,
			'label'   => 'Название',
			'attribs'  => array('class' => 'texter', 'style' => 'width:40%;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )));
			
			$save = $this->addElement('submit', 'fnfUpload', array(
            'required' => false,            
            'label'    => '>>',
			'decorators' => array( 'ViewHelper', 'Errors' )		
			));
			
			

			$this->setDecorators(array(
			array('viewScript', array('viewScript' => 'admin/forms/file.phtml'))
			));
		}

}