<?php
class Application_Form_RequestForm extends Zend_Form{
	
	public function init()
    {
    	$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
    	$this->setName('album');
    	$this->setMethod('post');

        $category = new Zend_Form_Element_Select('category');
        $category->setLabel('Категория заявки')
               ->setRequired(true)
               ->setRegisterInArrayValidator(false);

        $request = new Zend_Form_Element_Textarea('request');
        $request->setLabel('Заявка')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');
              
        $email = new Zend_Form_Element_Text('user_email');
        $email->setLabel('Ваша почта')      
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('EmailAddress');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Оставить заявку');

        $this->addElements(array($category, $request,$email, $submit));
    }	
    
    public function fillCategories($data){
    	$element = $this->getElement('category');
		foreach($data as $item){
			$element->addMultiOptions(array(
				$item['category_id'] => $item['name']
			));
		}
    }
	
}