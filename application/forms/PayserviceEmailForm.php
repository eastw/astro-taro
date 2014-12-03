<?php
class Application_Form_PayserviceEmailForm extends Zend_Form{
	
	protected $_positions;
	
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/payservice-email-form.phtml')),
		));
		
		$this->setName('email');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Почта для заказов')
					->setValidators(array('NotEmpty','EmailAddress'))
					->setDecorators($decorators)
					->setRequired(true);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Сохранить');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$this->addElements(array($email,$submit));
		
		$this->addDisplayGroup(array('submit'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
	}
}