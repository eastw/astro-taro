<?php
class Application_Form_WordForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/word-form.phtml')),
		));
		
		$this->setName('word');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$word = new Zend_Form_Element_Text('word');
		$word->setLabel('Слово')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setAttrib('style','width: 400px;')
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);

		$minidesc = new Zend_Form_Element_Textarea('minidesc');
		$minidesc->setLabel('Миниописание')
			->addFilter('StringTrim')
			->setAttrib('style','width: 400px;')
			->setValidators(array('NotEmpty'))
			->setDecorators($decorators);

		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Заголовок')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setAttrib('style','width: 400px;')
			->setValidators(array('NotEmpty'))
			->setDecorators($decorators);

		$keywords = new Zend_Form_Element_Text('keywords');
		$keywords->setLabel('Keywords')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setAttrib('style','width: 400px;')
			->setValidators(array('NotEmpty'))
			->setDecorators($decorators);

		$seodescription = new Zend_Form_Element_Textarea('seodescription');
		$seodescription->setLabel('Seo description')
			->addFilter('StringTrim')
			->setAttrib('style','width: 400px;')
			->setValidators(array('NotEmpty'))
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
		$cancel->setAttrib('onclick','window.history.back()');
		
		$this->addElements(array($word,$minidesc, $title, $keywords, $seodescription, $submit, $cancel,$page));
		
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
	}
}