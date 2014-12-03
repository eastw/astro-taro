<?php
class Application_Form_RecoverEmailForm extends Zend_Form{
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);

		$email = new Zend_Form_Element_Text('remail');
		$email->setLabel('Эл. почта')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty','EmailAddress',new App_Validate_RecoverExistEmail()));
		
		$submit = new Zend_Form_Element_Submit('submit');
		$this->addElements(array($email,$submit));
	}
	
}