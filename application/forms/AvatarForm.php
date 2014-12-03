<?php
class Application_Form_AvatarForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/forms/avatar-form.phtml')),
		));
		$this->setAction('/profile/change-avatar');
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		//$this->setIsArray(true);
		
		$this->setName('avatar');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$image = new Zend_Form_Element_File('image');
		$image->setRequired(true)
		->addValidator('Size', false, 10024000)
		->addValidator('Extension', false, 'jpg,png,gif')
		/*
		->addValidator('ImageSize', false,
                      array('minwidth' => 100,
                            'maxwidth' => 140,
                            'minheight' => 100,
                            'maxheight' => 140))
                        */
		->setDecorators(
			array(
				'File',
				'Errors',
				//array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				//array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		));
		
		$image->getValidator('Size')->setMessage('Не больше 1мб');
		//$image->getValidator('ImageSize')->setMessage('Разерешение не больше 140х140');
		$image->getValidator('Extension')->setMessage('Только jpg,png,gif');
		$image->addValidator('ImageSize', false, array(
							//'minwidth' => 100,
                            'maxwidth' => 140,
                            //'minheight' => 100,
                            'maxheight' => 140,
                            'messages'=>array(
                            	'fileImageSizeWidthTooBig'=>'Ширина не больше 140px',
								'fileImageSizeWidthTooSmall'=>'',
								'fileImageSizeHeightTooBig'=>'Высота не больше 140px',
								'fileImageSizeHeightTooSmall'=>'',
				)));
		
		$imgNote = new App_Form_Element_InfoLabel(
				'img_note',
				array('value' => '')
		);
		$imgNote->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				//array(array('data'=>'HtmlTag'), array('tag' => 'td')),
				//array('Errors'),
				//array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));
		
		$submit = new Zend_Form_Element_Submit('avatar_submit');
		$submit->setAttrib('class','avatar-update-off');
		$submit->setLabel('Изменить');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$cancel = new Zend_Form_Element_Button('cancel');
		$cancel->setAttrib('class','avatar-remove');
		$cancel->setLabel('Удалить');
		
		$cancel->setDecorators(array(
				'ViewHelper',
		));
		
		$this->addElements(array($image,$imgNote,$submit,$cancel));
	}
}