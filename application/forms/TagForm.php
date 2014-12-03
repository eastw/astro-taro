<?php
class Application_Form_TagForm extends Zend_Form{

	public function init()
	{
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);

		$this->setName('tag');
		$this->setMethod('post');
		
		$this->clearDecorators();
	
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);

		$tagname = new Zend_Form_Element_Text('tagname');
		$tagname->setLabel('Тег')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		 ->setDecorators($decorators);

		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Описание тега')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		 ->setDecorators($decorators);

		$keywords = new Zend_Form_Element_Text('seo-keywords');
		$keywords->setLabel('Ключевые слова')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		 ->setDecorators($decorators);
		
		$desc = new Zend_Form_Element_Textarea('seo-description');
		$desc->setLabel('SEO описание')
		->addFilter('StripTags')
		->addFilter('StringTrim')
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
		$cancel->setAttrib('onclick','window.location.href="/admin/tag"');
		
		$this->setAttrib('class', 'admin-form');
		
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'table')),
				'Form',
		));
		$this->addElements(array($tagname,$description,$keywords,$desc,$submit, $cancel,$page));
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right'))
		));
	}
}