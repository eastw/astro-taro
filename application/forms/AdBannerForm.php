<?php
class Application_Form_AdBannerForm extends Zend_Form{
	
	protected $_positions;
	
	public function init(){
		
	}
	
	public function initForm($positions = null){
		$this->setPositions($positions);
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/ad-banner-form.phtml','positions' => $this->_positions)),
		));
		
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		//$this->setIsArray(true);
		
		$this->setName('ad');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$imgDecorators = array(
						'File',
						'Errors',
						array(array('data'=>'HtmlTag'),array('tag'=>'td')),
						array('Label',array('tag'=>'td')),
						array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
				);
		$noteDecorators = array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
						array('Errors'),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
					);
		
		$type = new Zend_Form_Element_Select('type');
		$type->setLabel('Тип баннера')
			->setDecorators($decorators)
			//->setRegisterInArrayValidator(false)
			->addMultiOptions(array(
						'order' => 'Заказной',
						'partner' => 'Партнерка',
				));
					
		$image = new Zend_Form_Element_File('image');
		$image->setLabel('Картинка баннера')
				->addValidator('Size', false, 1024000)
				//->setRequired(true)
				->addValidator('Extension', false, 'jpg,png,gif')
				->setDecorators($imgDecorators);
		
		$imageNote = new App_Form_Element_InfoLabel(
				'image_note',
				array('value' => '')
		);
		$imageNote->setDecorators($noteDecorators);
		
		
		$link = new Zend_Form_Element_Text('link');
		$link->setLabel('Ссылка перехода для заказного баннера')
			->setValidators(array('NotEmpty'))
			->setDecorators($decorators);
			
		$startdate = new Zend_Form_Element_Text('startdate');
		$startdate->setLabel('Старт показа баннера')
			//->setRequired(true)
			->setValidators(array('NotEmpty'))
			->setDecorators($decorators);
			
		$enddate = new Zend_Form_Element_Text('enddate');
		$enddate->setLabel('Окончание показа баннера')
			->setValidators(array('NotEmpty'))
			->setDecorators($decorators);
			
			
		$code = new Zend_Form_Element_Textarea('code');
		$code->setLabel('Поле для кода партнерской системы ')
			->setDecorators($decorators);
		
		$banner = new Zend_Form_Element_Select('banner');
		$banner->setLabel('Баннер')
			->setDecorators($decorators)
			->addMultiOptions(array(
						'top' => 'В хидере',
						'bottom' => 'В футере',
						'right1' => 'Справа №1',
						'right2' => 'Справа №2',
						'right3' => 'Справа №3',
				));	 
			
		$through = new Zend_Form_Element_Select('through');
		$through->setLabel('Баннер сквозной')
			->setDecorators($decorators)
			//->setRegisterInArrayValidator(false)
			->addMultiOptions(array(
						'n' => 'Нет',
						'y' => 'Да',
				)); 	
	
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Сохранить');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$cancel = new Zend_Form_Element_Button('cancel');
		$cancel->setLabel('Отмена');
		
		$cancel->setDecorators(array(
				'ViewHelper',
		));
		$cancel->setAttrib('onclick','window.location.href="/admin/banner/ad"');
		
		//$this->setAttrib('class', 'article-form');
		/*
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'table')),
				'Form',
		));
		*/
		$this->addElements(array($type,$image,$imageNote,$link,$code,$through,$startdate,$enddate,$banner,$submit, $cancel));
		
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
	}
	
	public function setPositions($positions){
		$this->_positions = array(
			array('label' => 'Главная','value' => 'index','checked' => ''),
			array('label' => 'Статьи','value' => 'article','checked' => ''),
			array('label' => 'Новости','value' => 'news','checked' => ''),
			array('label' => 'Магия','value' => 'magic','checked' => ''),
			array('label' => 'Гадания','value' => 'divination','checked' => ''),
			array('label' => 'Нумерология','value' => 'numerology','checked' => ''),
			array('label' => 'Лунный календарь','value' => 'moon','checked' => ''),
			array('label' => 'Гороскопы','value' => 'horoscope','checked' => ''),
			array('label' => 'Профиль','value' => 'profile','checked' => ''),
			array('label' => 'Поиск','value' => 'search','checked' => ''),
			array('label' => 'Регистрация','value' => 'user','checked' => ''),
		);
		//var_dump($positions); die;
		if(null !== $positions && count($positions)){
			foreach($this->_positions as $index => $position){
				foreach($positions as $item){
					if($position['value'] == $item['position']){
						$this->_positions[$index]['checked'] = 'checked';
					}
				}
			}
		}
		//var_dump($this->_positions); die;
		//$this->positions = $positions;
	} 
}