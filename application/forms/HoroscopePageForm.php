<?php
class Application_Form_HoroscopePageForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/horoscope-page-form.phtml')),
		));
		
		$this->setName('page');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$type = new Zend_Form_Element_Select('page_type');
		$type->setLabel('Ссылка на страницу')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);
				
		$nameRu = new Zend_Form_Element_Text('name_ru');
		$nameRu->setLabel('Название на русском')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);
		
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Заголовок страницы (title)')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);
		
		$keywords = new Zend_Form_Element_Text('seokeywords');
		$keywords->setLabel('SEO ключвые слова(255 символов)')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setDecorators($decorators);
		
		$desc = new Zend_Form_Element_Textarea('seodescription');
		$desc->setLabel('SEO description(255 символов)')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setDecorators($decorators);
		
		$minidesc = new Zend_Form_Element_Textarea('minidesc');
		$minidesc->setLabel('Миниописание')
					->addFilter('StripTags')
					->addFilter('StringTrim')
					->addValidator('NotEmpty')
					->setDecorators($decorators);
		
		$page = new Zend_Form_Element_Hidden('page');
		$page->setDecorators(array(
				'ViewHelper',
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
		$cancel->setAttrib('onclick','window.location.href="/admin/horoscope/list-pages"');
		
		$this->addElements(array($type,$nameRu,$title,$keywords,$desc,$minidesc,$submit,$cancel,$page));
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
		$this->fillPageTypes();
	}
	
	protected function fillPageTypes(){
		$type = $this->getElement('page_type');
		$type->addMultiOptions(array(
			'' => 'Выберите тип страницы',
			'today' => 'Гороскоп на сегодня',
			'business-compability' => 'Партнерский гороскоп совместимости',
			'love-compability' => 'Любовный гороскоп совместимости',
			'simple' => 'Характеристика знака',
			'profession' => 'Гороскоп профессии',
			'karma' => 'Кармический гороскоп',
			'health' => 'Гороскоп здоровья',
			'child' => 'Гороскоп ребенка ',
			'business' => 'Бизнес гороскоп',
			'week' => 'Гороскоп на неделю',
			'month' => 'Гороскоп на месяц',
			'year' => 'Гороскоп на год',
			'next-year' => 'Гороскоп на следующий год'
		));
	}
}