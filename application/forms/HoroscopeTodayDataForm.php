<?php
class Application_Form_HoroscopeTodayDataForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/horoscope-today-data-form.phtml')),
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
		
		$sign = new Zend_Form_Element_Select('sign');
		$sign->setLabel('Знак')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setDecorators($decorators);
		
		$desc = new Zend_Form_Element_Textarea('description');
		$desc->setLabel('Текст гороскопа')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setDecorators($decorators);
		
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
		//$cancel->setAttrib('onclick','window.location.href="/admin/horoscope/today"');
		
		$this->addElements(array($sign,$desc,$submit,$cancel));
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
	}
	
	public function fillSigns($data){
		$sign = $this->getElement('sign');
		if(count($data)){
			foreach($data as $item){
				$sign->addMultiOptions(array(
						$item['id'] => $item['sign_ru'],
				));
			}
		}
	}
}