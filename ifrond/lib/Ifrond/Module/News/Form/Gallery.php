<?php
class Ifrond_Module_News_Form_Gallery extends Zend_Form
{

	public function init()
	{
		$this->setAction('/news/admin/gallerysave/');
		$this->setAttrib('id', 'News_Form_GalleryEdit');	

		$this->addElement('hidden', 'fngeId', array(
			'value' => -1,
			'filters'    => array('Int'),
			'required'   => false,			
			'decorators' => array( 'ViewHelper', 'Errors' )));
			
		$this->addElement('text', 'fngeTitle', array(
			'value' => '',
			'filters'    => array('StripTags', 'StringTrim'),
			'required'   => false,
			'label'   => 'Название',
			'attribs'  => array('class' => 'texter', 'style' => 'width:340px;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )));
			
		$save = $this->addElement('submit', 'fngeUpload', array(
            'required' => false,            
            'label'    => 'Сохранить',
			'decorators' => array( 'ViewHelper', 'Errors' )		
		));

		$this->setDecorators(array(
		array('viewScript', array('viewScript' => 'admin/forms/gallery.phtml'))
		));
	}

}