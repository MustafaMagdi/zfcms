<?php
class Ifrond_Module_Page_Form_Edit extends Zend_Form
{
	
	protected $_mapperPage = null;
	
	public function getCatTreeOptions() {
		$tree = $this->getCatTree();
		$tree = array_merge(array(0 => 'Корневой раздел'), $tree);
		return $tree;
	}
	
	public function setMapperPage($mapper) {
		$this->_mapperPage = $mapper;
	}
	
	public function getMapperPage() {
		if ($this->_mapperPage == null) {
			$this->_mapperPage = new Ifrond_Module_Page_Model_Mapper_Page();
		}		
		return $this->_mapperPage;
	}
	
	public function getCatTree() {
		$mapper = $this->getMapperPage(); 		
		$tree = $mapper->getTree()->getTree();
		$pageList = $this->getCatSubTree($tree->children(), 0);		
		return $pageList;
	}
	
	public function getCatSubTree($pages, $level) {
		$pageList = array();
		$level++;
		foreach ($pages as $t) {
			$pageList[(string) $t->attributes()->id] = str_repeat('...', ($level - 1)) . $t->attributes()->title;
			$pl = $this->getCatSubTree($t->children(), $level);			
			foreach ($pl as $k => $tt) {
				$pageList[$k] = $tt;
			}
		}
		return $pageList;		
	}
	
	public function init()
	{
		$this->setAction('/page/admin/save/')
		->setMethod('post')
		->setAttrib('id', 'Page_Form_Edit');

		$this->addElement('text', 'fpeTitle', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => 'Новая страница',
            'label'      => 'Заголовок',
			'attribs'  => array('style' => 'font-size:22px;width:98%;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('select', 'fpeIscat', array(
            'filters'    => array('Int'),
            'required'   => true,
            'label'      => 'Тип',
			'attribs'  => array('style' => 'width:130px;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));	
		$this->getElement('fpeIscat')->setMultiOptions(array(0 => 'страница сайта', 1 => 'раздел сайта'));
		
		$this->addElement('select', 'fpePid', array(
            'filters'    => array('Int'),
            'required'   => true,
            'label'      => 'Раздел',
			'attribs'  => array('style' => 'width:300px;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));		
		$this->getElement('fpePid')->setMultiOptions($this->getCatTreeOptions());

		$this->addElement('text', 'fpeTags', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(
		array('StringLength', false, array(3, 900)),
		),
            'required'   => false,
            'label'      => 'Тэги',
			'attribs'  => array('style' => 'width:230px;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));

		$this->addElement('textarea', 'fpeSubtitle', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(
		array('StringLength', false, array(3, 500)),
		),
            'required'   => false,
            'label'      => 'Подзаголовок',
		'attribs'  => array('class' => 'texter', 'style' => 'width:93%;height:57px;'),
		'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('text', 'fpeShortcut', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(
		array('StringLength', false, array(3, 500)),
		),
            'required'   => false,
            'label'      => 'Ярлык',		
		'attribs'  => array('style' => 'width:230px;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('textarea', 'fpeText', array(
		    'label'      => 'Текст',
		    'required'   => false,
			'attribs'  => array('class' => 'wysiwyg', 'style' => 'width:100%;height:400px;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));

		$this->addElement('hidden', 'fpeId', array(
			'value' => 0,
			'filters'    => array('Int'), 
			'required'   => true,
			'decorators' => array( 'ViewHelper', 'Errors' )));
		
		$this->addElement('hidden', 'fpeSeotitle', array(
			'value' => '',
			'filters'    => array('StripTags', 'StringTrim'), 
			'required'   => false,
			'decorators' => array( 'ViewHelper', 'Errors' )));
		
		$this->addElement('hidden', 'fpeSeodescription', array(
			'value' => '',
			'filters'    => array('StripTags', 'StringTrim'), 
			'required'   => false,
			'decorators' => array( 'ViewHelper', 'Errors' )));
		
		$this->addElement('hidden', 'fpeSeokeywords', array(
			'value' => '',
			'filters'    => array('StripTags', 'StringTrim'), 
			'required'   => false,
			'decorators' => array( 'ViewHelper', 'Errors' )));
		
		$this->addElement('hidden', 'fpeRang', array(
			'value' => -1,
			'filters'    => array('Int'), 
			'required'   => true,
			'decorators' => array( 'ViewHelper', 'Errors' )));
				
		$this->setDecorators(
		array(
			array('viewScript', array('viewScript' => '/admin/forms/edit.phtml'))
		));




	}
}