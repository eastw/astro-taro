<?php
class Application_Form_FeedbackForm extends Zend_Form{
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);

		$this->setName('feedback');
		$this->setMethod('post');
		
		
		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty'));
		
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Эл. почта')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty','EmailAddress'));
		$email->getValidator('NotEmpty')->setMessage('Обязательное поле');
		$email->getValidator('EmailAddress')->setMessage('Обязательное поле');
		
		$content = new Zend_Form_Element_Textarea('content');
		$content->setLabel('')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty'));
			
		
		$this->addElements(array($name,$email,$content));
	}
}