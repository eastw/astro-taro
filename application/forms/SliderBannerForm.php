<?php
class Application_Form_SliderBannerForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/slider-banner-form.phtml')),
		));
		
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		//$this->setIsArray(true);
		
		$this->setName('slider');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$imgDecorators = array(
						'File',
						'Errors',
						array(array('data'=>'HtmlTag'),array('tag'=>'td')),
						array('Label',array('tag'=>'td')),
						array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
				);
		$noteDecorators = array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
						array('Errors'),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
					);
		
		/*			
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Название колоды')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);
		*/		
		
		$image = new Zend_Form_Element_File('image');
		$image->setLabel('Картинка баннера(1016х340)')
				->addValidator('Size', false, 1024000)
				->setRequired(true)
				->addValidator('Extension', false, 'jpg,png,gif')
				->setDecorators($imgDecorators);
		
		$imageNote = new App_Form_Element_InfoLabel(
				'image_note',
				array('value' => '')
		);
		$imageNote->setDecorators($noteDecorators);
		
		$link = new Zend_Form_Element_Text('link');
		$link->setLabel('Ссылка на внутренний раздел')
			->setDecorators($decorators);
		
		$type = new Zend_Form_Element_Select('type');
		$type->setLabel('Платные услуги')
			->setDecorators($decorators)
			->setRegisterInArrayValidator(false)
			->addMultiOptions(array(
						'n' => 'Нет',
						'y' => 'Да',
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
		$cancel->setAttrib('onclick','window.location.href="/admin/banner/slider"');
		
		//$this->setAttrib('class', 'article-form');
		/*
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'table')),
				'Form',
		));
		*/
		$this->addElements(array($image,$imageNote,$link,$type,$submit, $cancel));
		
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
	}
}