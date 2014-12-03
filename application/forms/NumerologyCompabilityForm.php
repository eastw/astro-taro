<?php
class Application_Form_NumerologyCompabilityForm extends Zend_Form{
	
	protected $type;
	protected $userdata; 
	
	public function init(){
		$this->type = 'love';
	}
	
	public function setFormType($type){
		$this->type = $type;
	}
	
	public function setUserdata($userdata = null){
		$this->userdata = $userdata;
	}
	
	public function startform(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'ViewScript',
				array('ViewScript', array('viewScript' => 'partials/forms/numerology-compability-form.phtml','type' => $this->type,'userdata' =>$this->userdata)),
		));
		
		$this->setName('lifepath');
		$this->setMethod('post');
		
		$fname1 = new Zend_Form_Element_Text('fname1');
		$fname1	->addFilter('StripTags')
		->setLabel('1')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$mname1 = new Zend_Form_Element_Text('mname1');
		$mname1->setLabel('1')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$lname1 = new Zend_Form_Element_Text('lname1');
		$lname1->addFilter('StripTags')
		->setLabel('1')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));//->setAttrib('style', 'width:100%');
		
		$day1 = new Zend_Form_Element_Select('bday1');
		$day1->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setRegisterInArrayValidator(false)
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		))
		->addMultioptions(array('' => 'день'));
		
		$month1 = new Zend_Form_Element_Select('bmonth1');
		$month1->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$year1 = new Zend_Form_Element_Select('byear1');
		$year1->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$fname2 = new Zend_Form_Element_Text('fname2');
		$fname2	->addFilter('StripTags')
		->setLabel('1')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$mname2 = new Zend_Form_Element_Text('mname2');
		$mname2->setLabel('1')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$lname2 = new Zend_Form_Element_Text('lname2');
		$lname2->addFilter('StripTags')
		->setLabel('1')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));//->setAttrib('style', 'width:100%');
		
		$day2 = new Zend_Form_Element_Select('bday2');
		$day2->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setRegisterInArrayValidator(false)
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		))
		->addMultioptions(array('' => 'день'));
		
		$month2 = new Zend_Form_Element_Select('bmonth2');
		$month2->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$year2 = new Zend_Form_Element_Select('byear2');
		$year2->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$smalltype = new Zend_Form_Element_Hidden('smalltype');
		$smalltype->setDecorators(array(
				'ViewHelper',
		));
		
		$submit = new Zend_Form_Element_Button('submit');
		$submit->setLabel('Отправить данные');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$this->addElements(array($fname1,$mname1,$lname1,$year1,$month1,$day1,$fname2,$mname2,$lname2,$year2,$month2,$day2,$smalltype,$submit));
		$this->fillMonthes();
		$this->fillYears();
	}
	
	private function fillYears(){
		$years = array('' => 'год');
		for($i = date('Y',strtotime(date('Y').' + 10 year')); $i > 1930; $i--){
			$years[$i] = $i;
		}
		$this->byear1->addMultiOptions($years);
		$this->byear2->addMultiOptions($years);
	}
	
	private function fillMonthes(){
		$monthes = array(
				'' => 'месяц',
				'01' => 'Январь',
				'02' => 'Февраль',
				'03' => 'Март',
				'04' => 'Апрель',
				'05' => 'Май',
				'06' => 'Июнь',
				'07' => 'Июль',
				'08' => 'Август',
				'09' => 'Сентябрь',
				'10' => 'Октябрь',
				'11' => 'Ноябрь',
				'12' => 'Декабрь'
		);
		$this->bmonth1->addMultiOptions($monthes);
		$this->bmonth2->addMultiOptions($monthes);
	}
}