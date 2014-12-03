<?php
class Application_Form_DeckImageForm extends Zend_Form{

	public function init(){

		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);

		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/deck-image-form.phtml')),
		));
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		$this->setAction('/admin/divination/save-deck-card');

		$this->setName('deck');
		$this->setMethod('post');
		
		$normal = new Zend_Form_Element_File('normal');
		$normal	->setRequired(false)
				//->addValidator('Size', false, 1024000)
				//->addValidator('Extension', false, 'jpg,png,gif')
				->setDecorators(
						array(
							'File',
							'Errors',
							array(array('data'=>'HtmlTag'),array('tag'=>'td')),
							//array('Label',array('tag'=>'td')),
							array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
					));
		
		$normalNote = new App_Form_Element_InfoLabel(
				'normal_note',
				array('value' => '')
		);
		$normalNote->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
				array('Errors'),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));
		
		$reverse = new Zend_Form_Element_File('reverse');
		$reverse->setRequired(false)
				//->addValidator('Size', false, 1024000)
				//->addValidator('Extension', false, 'jpg,png,gif')
				->setDecorators(
						array(
							'File',
							'Errors',
							array(array('data'=>'HtmlTag'),array('tag'=>'td')),
							//array('Label',array('tag'=>'td')),
							array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
					));
		
		$reverseNote = new App_Form_Element_InfoLabel(
				'reverse_note',
				array('value' => '')
		);
		$reverseNote->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
				array('Errors'),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));

		$id = new Zend_Form_Element_Hidden('id');
		$id->setDecorators(array(
				'ViewHelper',
		));
		$number = new Zend_Form_Element_Hidden('number');
		$number->setDecorators(array(
				'ViewHelper',
		));
				
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Загрузить')
				->setDecorators(array(
						'ViewHelper',
				));
		
		$this->addElements(array($normalNote,$normal,$reverseNote,$reverse,$id,$number,$submit));
		/*
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'table')),
				'Form',
		));
		*/
	}
}