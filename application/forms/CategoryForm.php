<?php
class Application_Form_CategoryForm extends Zend_Form{
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate); 
		
		$this->clearDecorators();
		$decorators = array(
				'ViewHelper',
				'Errors',
				'Label',
				array('data2' => 'HtmlTag',array('tag'=>'div','class' => 'form-box'))
		);
		
		$name = new Zend_Form_Element_Text('name');
        $name->setLabel('Название категории')
               ->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim')
               ->addValidator('NotEmpty')
               ->setDecorators($decorators);
   
        $active = new Zend_Form_Element_Select('active');
        $active->setLabel('Активность на сайте')
		        ->setMultiOptions(array('y'=>'Да', 'n'=>'Нет'))
		        ->setDecorators($decorators)
				->setValue("y");
		
        $desc = new Zend_Form_Element_Textarea('desc');
        $desc->setLabel('Описание категории')
              ->addFilter('StringTrim')
              ->setDecorators($decorators);
         //$title->getValidator('NotEmpty')->setMessage('Too Old!!!');
              

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
        $cancel->setAttrib('onclick','window.location.href="/admin/category"');
        
        $this->setAttrib('class', 'admin-form');
        $this->setDecorators(array(
        	'FormElements',
	        array('HtmlTag', array('tag' => 'fieldset')),
	        'Form',
        ));

        $this->addElements(array($name, $active,$desc, $submit,$cancel));
       
	}
}