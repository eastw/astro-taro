<?php
class Application_Form_NumerologyForecastForm extends Zend_Form{
	
	protected $userdata;
	
	public function init(){
		
	}
	
	public function setUserdata($userdata=null){
		$this->userdata = $userdata;
	}
	
	public function startform(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/forms/numerology-forecast-form.phtml','userdata' => $this->userdata)),
		));
		
		$this->setName('lifepath');
		$this->setMethod('post');
		
		$fname = new Zend_Form_Element_Text('fname');
		$fname	->addFilter('StripTags')
		->setLabel('1')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$mname = new Zend_Form_Element_Text('mname');
		$mname->setLabel('1')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$lname = new Zend_Form_Element_Text('lname');
		$lname	->addFilter('StripTags')
		->setLabel('1')
		->addFilter('StringTrim')
		->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$day = new Zend_Form_Element_Select('bday');
		$day->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setRegisterInArrayValidator(false)
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		))
		->addMultioptions(array('' => 'день'));
		
		$month = new Zend_Form_Element_Select('bmonth');
		$month->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$year = new Zend_Form_Element_Select('byear');
		$year->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$pday = new Zend_Form_Element_Select('pday');
		$pday->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setRegisterInArrayValidator(false)
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		))
		->addMultioptions(array('' => 'день'));
		
		$pmonth = new Zend_Form_Element_Select('pmonth');
		$pmonth->setRequired(true)
		->setValidators(array('NotEmpty'))
		->setDecorators(array(
				'ViewHelper',
				'Errors',
		));
		
		$pyear = new Zend_Form_Element_Select('pyear');
		$pyear->setRequired(true)
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
		
		$this->addElements(array($fname,$mname,$lname,$year,$month,$day,$pyear,$pmonth,$pday,$smalltype,$submit));
		$this->fillMonthes();
		$this->fillYears();
	}
	
	private function fillYears(){
		$years = array('' => 'год');
		for($i = date('Y',strtotime(date('Y').' + 10 year')); $i > 1930; $i--){
			$years[$i] = $i;
		}
		$this->byear->addMultiOptions($years);
		$this->pyear->addMultiOptions($years);
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
		$this->bmonth->addMultiOptions($monthes);
		$this->pmonth->addMultiOptions($monthes);
	}
}