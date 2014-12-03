<?php
class Application_Form_MoonPhaseForm extends Zend_Form{
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);

		$this->setName('moon-phase');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Название лунной фазы')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty'))
			->setDecorators($decorators)
			->setAttrib('style', 'width:655px;');
		
		$short_desc = new Zend_Form_Element_Text('short_desc');
		$short_desc->setLabel('Короткое описание')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators($decorators)
		->setAttrib('style', 'width:655px;');
		
		$desc = new Zend_Form_Element_Textarea('desc');
		$desc->setLabel('Описание лунной фазы')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators($decorators);
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Сохранить');
		$submit->setDecorators(array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td','colspan' =>'2','style' => 'text-align:right')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		));
		
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'table')),
				'Form',
		));
		
		$this->addElements(array($name,$short_desc,$desc,$submit));
	}
}