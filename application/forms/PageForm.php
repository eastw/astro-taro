<?php
class Application_Form_PageForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/page-form.phtml')),
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
		
		$url = new Zend_Form_Element_Text('url');
		$url->setLabel('Ссылка на страницу')
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
		$cancel->setAttrib('onclick','window.location.href="/admin/pages"');
		
		
		$this->addElements(array($url,$nameRu,$title,$keywords,$desc,$minidesc,$submit,$cancel,$page));
		
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
	}
}