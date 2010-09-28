<?php
class Ifrond_Module_Feedback_Form_Feedback extends Zend_Form
{

	protected $_fileValidators = array(
		'Count' => 1,
		'Size' => 3145728,
		'Extension' => 'jpg,png,gif,jpeg',
		'ExcludeExtension' => ''
		);
		
	protected $_fileDestination;
	
	protected $_region = array(
	0 => '================================',
	1 => 'Агинский Бурятский автономный округ',
	2 => 'Алтайский край',
	3 => 'Амурская область',
	4 => 'Архангельская область',
	5 => 'Астраханская область',
	6 => 'Белгородская область',
	7 => 'Брянская область',
	8 => 'Владимирская область',
	9 => 'Волгоградская область',
	10 => 'Вологодская область',
	11 => 'Воронежская область',
	12 => 'Еврейская автономная область',
	13 => 'Ивановская область',
	14 => 'Иркутская область',
	15 => 'Кабардино-БалкарскаяРеспублика',
	16 => 'Калининградская область',
	17 => 'Калужская область',
	18 => 'Камчатская область',
	19 => 'Карачаево-Черкесская Республика',
	20 => 'Кемеровская область',
	21 => 'Кировская область',
	22 => 'Коми-Пермяцкий автономный округ',
	23 => 'Корякский автономный округ',
	24 => 'Костромская область',
	25 => 'Краснодарский край',
	26 => 'Красноярский край',
	27 => 'Курганская область',
	28 => 'Курская область',
	29 => 'Ленинградская область',
	30 => 'Липецкая область',
	31 => 'Магаданская область',
	32 => 'Московская область',
	33 => 'Мурманская область',
	34 => 'Ненецкий автономный округ',
	35 => 'Нижегородская область',
	36 => 'Новгородская область',
	37 => 'Новосибирская область',
	38 => 'Омская область',
	39 => 'Оренбургская область',
	40 => 'Орловская область',
	41 => 'Пензенская область',
	42 => 'Пермская область',
	43 => 'Приморский край',
	44 => 'Псковская область',
	45 => 'Республика Адыгея (Адыгея)',
	46 => 'Республика Алтай',
	47 => 'Республика Башкортостан',
	48 => 'Республика Бурятия',
	49 => 'Республика Дагестан',
	50 => 'Республика Ингушетия',
	51 => 'Республика Калмыкия',
	52 => 'Республика Карелия',
	53 => 'Республика Коми',
	54 => 'Республика Марий Эл',
	55 => 'Республика Мордовия',
	56 => 'Республика Саха (Якутия)',
	57 => 'Республика Северная Осетия - Алания',
	58 => 'Республика Татарстан (Татарстан)',
	59 => 'Республика Тыва',
	60 => 'Республика Хакасия',
	61 => 'Ростовская область',
	62 => 'Рязанская область',
	63 => 'Самарская область',
	64 => 'Саратовская область',
	65 => 'Сахалинская область',
	66 => 'Свердловская область',
	67 => 'Смоленская область',
	68 => 'Ставропольский край',
	69 => 'Таймырский (Долгано-Ненецкий) автономный',
	70 => 'Тамбовская область',
	71 => 'Тверская область',
	72 => 'Тверская область',
	73 => 'Томская область',
	74 => 'Тульская область',
	75 => 'Тюменская область',
	76 => 'Удмуртская Республика',
	77 => 'Ульяновская область',
	78 => 'Усть-Ордынский Бурятский автономный округ',
	79 => 'Хабаровский край',
	80 => 'Ханты-Мансийский автономный округ',
	81 => 'Челябинская область',
	82 => 'Читинская область',
	83 => 'Чувашская Республика - Чуваш республики',
	84 => 'Чукотский автономный округ',
	85 => 'Эвенкийский автономный округ',
	86 => 'Ямало-Ненецкий автономный округ',
	87 => 'Ярославская область',
	88 => 'г. Москва',
	89 => 'г. Санкт-Петербург',
	90 => 'Страны СНГ',
	91 => 'Иное'
	);

	protected $_district = array(
	0 => '================================',
	1 => 'Ульяновск',
	2 => 'Димитровград',
	3 => 'Барыш',
	4 => 'Инза',
	5 => 'Базарносызганский район',
	6 => 'Барышский район',
	7 => 'Вешкаймский район',
	8 => 'Инзенский район',
	9 => 'Карсунский район',
	10 => 'Кузоватовский район',
	11 => 'Майнский район',
	12 => 'Мелекесский район',
	13 => 'Николаевский район',
	14 => 'Новомалыклинский район',
	15 => 'Новоспасский район',
	16 => 'Павловский район',
	17 => 'Радищевский район',
	18 => 'Сенгилеевский район',
	19 => 'Старокулаткинский район',
	20 => 'Старомайнский район',
	21 => 'Сурский район',
	22 => 'Тереньгульский район',
	23 => 'Ульяновский район',
	24 => 'Цильнинский район',
	25 => 'Чердаклинский район'
	);

	protected $_occupation = array(
	0 => '================================',
	1 => 'Военнослужащий',
	2 => 'Не работающий',
	3 => 'Пенсионер',
	4 => 'Предприниматель',
	5 => 'Работник агропромышленного комплекса',
	6 => 'Работник науки и культуры',
	7 => 'Работник сферы производства и обслуживания',
	8 => 'Служащий',
	9 => 'Учащийся'
	);

	protected $_privilege = array(
	0 => '================================',
	1 => 'Инвалиды войны',
	2 => 'Участники войны',
	3 => 'Военнослужащие',
	4 => 'Офицеры запаса',
	5 => 'Воины-интернационалисты',
	6 => 'Участники войны в Чечне',
	7 => 'Группа особого риска',
	8 => 'Семьи погибших и пропавших без вести',
	9 => 'Инвалиды труда',
	10 => 'Инвалиды 1 группы и онкобольные',
	11 => 'Инвалиды по психзаболеванию',
	12 => 'Больные заразной формой туберкулеза',
	13 => 'Инвалиды по общему заболеванию',
	14 => 'Вдовы',
	15 => 'Воспитанники детских домов',
	16 => 'Многодетные матери',
	17 => 'Матери-одиночки',
	18 => 'Опекуны',
	19 => 'Ветераны',
	20 => 'Граждане, имеющие почетные звания',
	21 => 'Депутаты',
	22 => 'Проживающие в аварийных домах',
	23 => 'Беженцы',
	24 => 'Репрессированные',
	25 => 'Участники ликвидации Чернобыльской аварии',
	26 => 'Труженики тыла'
	);

	protected $_federal = array(
	0 => '================================',
	1 => 'Федеральные органы исполнительной власти',
	2 => 'Администрация области',
	3 => 'Законодательное собрание области'
	);

	protected $_local = array(
	0 => '================================',
	1 => 'Мэрия города, администрация района',
	2 => 'Сельский, поселковый совет',
	3 => 'Администрация района города'
	);

	protected $_topic = array(
	0 => '================================',
	1 => 'Вопросы промышленности',
	2 => 'Вопросы строительства',
	3 => 'Транспорт',
	5 => 'Связь',
	6 => 'Вопросы труда и зарплаты',
	7 => 'Агропромышленный комплекс',
	8 => 'Государство, общество, политика',
	9 => 'Наука, культура, спорт',
	10 => 'Народное образование',
	11 => 'Торговля',
	12 => 'Вопросы жилья',
	13 => 'Коммунально-бытовое хозяйство',
	14 => 'Социальное обеспечение и защита населения',
	15 => 'Финансовые вопросы',
	16 => 'Здравоохранение',
	17 => 'Работа правоохранительных органов и ВС',
	18 => 'Работа с обращениями граждан',
	19 => 'Экология и природопользование'
	);

	public function getOptionTitle($type, $id) {
		$param = '_'.$type;
		$option = $this->$param;
		if (isset($option[$id])) return $option[$id];
		else return false;
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

	public function init()
	{
		$this->setAction('/feedback/index/send/')
		->setMethod('post')
		->setAttrib('enctype', 'multipart/form-data')
		->setAttrib('id', 'feedback');
			
		$this->addElement('text', 'person_family', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => '',
            'label'      => 'Фамилия',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('text', 'person_name', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => '',
            'label'      => 'Имя',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('text', 'person_secondname', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => false,
			'value' => '',
            'label'      => 'Отчество',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('select', 'person_occupation', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'required'   => false,
			'value' => 0,
            'label'      => 'Род занятий',
			'attribs'  => array('class' => 'texter'),
			'multiOptions' => $this->_occupation,
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('select', 'person_privilege', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'required'   => false,
			'value' => 0,
            'label'      => 'Ваши социальные льготы',
			'attribs'  => array('class' => 'texter'),
			'multiOptions' => $this->_privilege,
			'decorators' => array( 'ViewHelper', 'Errors' )
		));



		$this->addElement('text', 'address_index', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => '',
            'label'      => 'Индекс',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('select', 'address_region', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'required'   => false,
			'value' => 0,
            'label'      => 'Регион',
			'attribs'  => array('class' => 'texter'),
			'multiOptions' => $this->_region,
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('select', 'address_district', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'required'   => false,
			'value' => 0,
            'label'      => 'Район Ульяновской области',
			'attribs'  => array('class' => 'texter'),
			'multiOptions' => $this->_district,
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('text', 'address_address', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => '',
            'label'      => 'Почтовый адрес',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));


		$this->addElement('text', 'connection_email', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900)), 'EmailAddress'),
            'required'   => false,
			'value' => '',
            'label'      => 'Электронная почта',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('text', 'connection_phone', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => false,
			'value' => '',
            'label'      => 'Телефон',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('text', 'connection_fax', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => false,
			'value' => '',
            'label'      => 'Факс',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));


		$this->addElement('select', 'power_federal', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'required'   => false,
			'value' => 0,
            'label'      => 'Органы федеральной власти',
			'attribs'  => array('class' => 'texter'),
			'multiOptions' => $this->_federal,
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('text', 'power_federaldate', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => false,
			'value' => '',
            'label'      => 'Дата обращения',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('select', 'power_local', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'required'   => false,
			'value' => 0,
            'label'      => 'Органы местного самоуправления',
			'attribs'  => array('class' => 'texter'),
			'multiOptions' => $this->_local,
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('text', 'power_localdate', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => false,
			'value' => '',
            'label'      => 'Дата обращения',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('text', 'power_other', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => false,
			'value' => '',
            'label'      => 'Иные органы и организации',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('text', 'power_otherdate', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => false,
			'value' => '',
            'label'      => 'Дата обращения',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));


		$this->addElement('select', 'msg_topic', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'required'   => false,
			'value' => 0,
            'label'      => 'Суть вопроса',
			'attribs'  => array('class' => 'texter'),
			'multiOptions' => $this->_topic,
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		$this->addElement('textarea', 'msg_msg', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'required'   => true,
			'value' => '',
            'label'      => 'Текст запроса',
			'attribs'  => array('class' => 'texter', 'style' => 'width:500px;height:250px;'),			
			'decorators' => array( 'ViewHelper', 'Errors' )
		));

		$this->addElement($this->addFile('att_files_1'));
		$this->addElement($this->addFile('att_files_2'));
		$this->addElement($this->addFile('att_files_3'));
		$this->addElement($this->addFile('att_files_4'));
		$this->addElement($this->addFile('att_files_5'));

		$this->addElement('submit', 'save', array(
            'required' => false,            
            'label'    => 'Отправить',		
			'attribs'  => array('style' => 'font-size:18px'),		
			'decorators' => array( 'ViewHelper', 'Errors' )
		));

		$this->setDecorators(array(array('viewScript', array('viewScript' => '/index/forms/feedback.phtml'))));

	}
	
	function addFile($name) {
		$element = new Zend_Form_Element_File($name);
		$element->setLabel('Прикрепить файл');
		$element->setDestination($this->_fileDestination);		
		$element->setDecorators(array('File', 'Description', 'Errors'));
		$element->setAttribs(array('class' => 'texter'));		
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
		$element->setRequired(false);
		return $element;	
	}
}