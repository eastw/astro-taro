<?php
class Application_Form_AuthForm extends Zend_Form{
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);

		$this->setName('registration');
		$this->setMethod('post');
		
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Эл. почта')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty','EmailAddress'));
		
		$pass = new Zend_Form_Element_Password('pass');
		$pass->setLabel('Пароль')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'));
		
		$this->addElements(array($email,$pass));
	}
}