<?php
class Application_Form_PayserviceGateForm extends Zend_Form{
	
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/payservice-gate-form.phtml')),
		));
		
		//$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		//$this->setIsArray(true);
		
		$this->setName('gate');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$gate = new Zend_Form_Element_Text('gate');
		$gate->setLabel('Название платежной системы')
					->setValidators(array('NotEmpty'))
					->setDecorators($decorators)
					->setRequired(true);

		$details = new Zend_Form_Element_Textarea('details');
		$details->setLabel('Реквизиты системы(будут высланы заказчику)')
					->setValidators(array('NotEmpty'))
					->setDecorators($decorators)
					->setRequired(true);
		
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
		$cancel->setAttrib('onclick','window.location.href="/admin/payservice/gates"');
		
		$this->addElements(array($gate,$details,$submit, $cancel));
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
	}
	
	public function initForm($types = null){
	}
	
	protected  function setTypes(){
		$type = $this->getElement('type');
		$types = App_PayserviceService::getPayTypes();
		foreach ($types as $index => $item){
			$type->addMultioptions(array(
				$index => $item
			));
		}
	} 
}