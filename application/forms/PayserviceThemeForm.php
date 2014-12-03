<?php
class Application_Form_PayserviceThemeForm extends Zend_Form{
	
	protected $_positions;
	
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/payservice-theme-form.phtml')),
		));
		
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		//$this->setIsArray(true);
		
		$this->setName('theme');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$type = new Zend_Form_Element_Select('type');
		$type->setLabel('Тип сервиса')
			->setDecorators($decorators);
			
		
		$theme = new Zend_Form_Element_Text('theme');
		$theme->setLabel('Название темы')
					->setValidators(array('NotEmpty'))
					->setDecorators($decorators)
					->setRequired(true);

		$cost = new Zend_Form_Element_Text('cost');
		$cost->setLabel('Стоимость заказа темы, грн')
					->setValidators(array('NotEmpty'))
					->setDecorators($decorators)
					->setRequired(true);
					
		$doubleForm = new Zend_Form_Element_Select('double_form');
		$doubleForm->setLabel('Требует данных партнера')
					->setDecorators($decorators)
					->setRequired(true)
					->addMultioptions(array(
						'n' => 'Нет',
						'y' => 'Да'
					));
					
		$image = new Zend_Form_Element_File('image');
		$image->setLabel('Картинка анонса')
		->addValidator('Size', false, 1024000)
		->addValidator('Extension', false, 'jpg,png,gif')
		->setDecorators(
			array(
				'File',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		));
		
		$imgNote = new App_Form_Element_InfoLabel(
				'img_note',
				array('value' => '')
		);
		$imgNote->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
				array('Errors'),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));
					
		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Описание темы')
					->setValidators(array('NotEmpty'))
					->setDecorators($decorators);
					
		$seotitle = new Zend_Form_Element_Text('seotitle');
		$seotitle->setLabel('SЕО title')
					->setValidators(array('NotEmpty'))
					->setDecorators($decorators);
					
		$seokeywords = new Zend_Form_Element_Text('seokeywords');
		$seokeywords->setLabel('SЕО keywords')
					->setValidators(array('NotEmpty'))
					->setDecorators($decorators);
					
		$seodescription = new Zend_Form_Element_Textarea('seodescription');
		$seodescription->setLabel('SЕО description')
					->setValidators(array('NotEmpty'))
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
		$cancel->setAttrib('onclick','window.location.href="/admin/payservice/themes"');
		
		$this->addElements(array($type,$theme,$doubleForm,$cost,$image,$imgNote,$description,$seotitle,$seokeywords,$seodescription,$submit, $cancel));
		
		$this->setTypes();
		
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