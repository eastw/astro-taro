<?php
class Application_Form_MoonDayForm extends Zend_Form{
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
		
		$number = new Zend_Form_Element_Select('number');
		$number->setLabel('Номер дня')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty','Digits'))
			->setDecorators($decorators)
			->setAttrib('style', 'width:655px;');
		
		$phase = new Zend_Form_Element_Select('phase');
		$phase->setLabel('Лунная фаза')
			->setRequired(true)
			->setValidators(array('NotEmpty'))
			->setDecorators($decorators)
			->setAttrib('style', 'width:655px;');
		
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
		));
		/*
		$submit->setDecorators(array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td','colspan' =>'2','style' => 'text-align:right')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		));
		*/
		
		$cancel = new Zend_Form_Element_Button('cancel');
		$cancel->setLabel('Отмена');
		$cancel->setDecorators(array(
				'ViewHelper',
		));
		$cancel->setAttrib('onclick','window.location.href="/admin/moon"');
		
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'table')),
				'Form',
		));
		
		$this->addElements(array($number,$phase,$imgNote,$image,$desc,$submit,$cancel));
		$this->fillNumber();
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
	}
	
	public function fillPhases($data){
		$phase = $this->getElement('phase');
		if(count($data)){
			foreach($data as $item){
				$phase->addMultiOptions(array(
					$item['id'] => $item['phase'],
				));
			}
		}
	}
	protected function fillNumber(){
		$number = $this->getElement('number');
		for($i = 1; $i < 31;$i++){
			$number->addMultiOptions(array(
					$i => $i,
			));
		}
	}
}