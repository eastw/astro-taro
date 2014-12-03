<?php
class Application_Form_PayserviceDivinationThemeForm extends Zend_Form{
	
	protected $_positions;
	
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/forms/payservice-divination-theme-form.phtml')),
		));
		
		//$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		//$this->setIsArray(true);
		
		$this->setName('theme');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
		);
		
		$name = new Zend_Form_Element_Text('name');
		$name
			->setDecorators($decorators)
			->setValidators(array('NotEmpty'))
			->setRequired(true)
			->setAttrib('class', 'reg_form');
			
		$year = new Zend_Form_Element_Select('year');
		$year->setDecorators($decorators)
					->setRequired(true);
		$month = new Zend_Form_Element_Select('month');
		$month->setDecorators($decorators)
					->setRequired(true);
		$day = new Zend_Form_Element_Select('day');
		$day->setDecorators($decorators)
				->setRegisterInArrayValidator(false)
					->setRequired(true);
					
		$email = new Zend_Form_Element_Text('email');
		$email
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty','EmailAddress'))
			->setAttrib('class','reg_form')
			->setAttrib('style', 'margin-bottom:5px')
			->setDecorators($decorators);
			
		$question = new Zend_Form_Element_Textarea('quest');
		$question
			->setDecorators($decorators)
			->setValidators(array('NotEmpty'))
			->setAttrib('class', 'primechanie')
			->setAttrib('placeholder', 'Опишите подробно ваш вопрос')
			->setAttrib('style', 'height:100px;')
			->setRequired(true);
			
		$paymentType = new Zend_Form_Element_Select('payment_type');
		$paymentType->setDecorators($decorators)
					->setRegisterInArrayValidator(false)
					->setRequired(true);
					
		$formType = new Zend_Form_Element_Hidden('form_type');
		$formType->setDecorators($decorators)->setValue('divination');
		
		$partner = new Zend_Form_Element_Hidden('partner');
		$partner->setDecorators($decorators)->setValue('n');
		
		$alias = new Zend_Form_Element_Hidden('alias');
		$alias->setDecorators($decorators);
		
		$summ = new App_Form_Element_InfoLabel(
				'summ',
				array('value' => '')
		);
		$summ->setDecorators(array(
				'ViewHelper',
		));

		$this->addElement(
			'checkbox', 'agree', array(
					'required' => true,
					'uncheckedValue' => '',
					'checkedValue' => '1'
			)
		);
		$this->agree->addValidator('NotEmpty');
		
		$this->agree->setDecorators(array(
				'ViewHelper',
		))->setAttrib('class','check')->getValidator('NotEmpty')->setMessage('Чтобы сделать заказ, необходимо принять наши Условия использования');
					
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Сохранить');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$this->addElements(array($name,$year,$month,$day,$email,$question,$paymentType,$formType,$partner,$alias,$summ,$submit));
		
		$this->fillMonthes();
		$this->fillYears();
		//$this->fillDays();
	}
	
	private function fillYears(){
		$years = array();//array('' => 'год');
		for($i = date('Y'); $i > 1930; $i--){
			$years[$i] = $i;
		}
		$this->year->addMultiOptions($years);
	}
	
	private function fillMonthes(){
		$monthes = array(
				//'' => 'месяц',
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
		$this->month->addMultiOptions($monthes);
	}
	
	public function fillDays($year=null,$month=null){
		if(!$year){
			$year = date('Y');
		}
		if(!$month){
			$month = date('m');
		}
		$num = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 31
		$days = array();
		for($i = 1; $i < ($num + 1); $i++){
			$days[$i] = $i;
		}
		$this->day->setMultiOptions($days);
	}
	
	public function fillPayGates($data){
		$paymentType = $this->getElement('payment_type');
		foreach($data as $item){
			$paymentType->addMultiOptions(array(
				$item['gate'] => $item['gate']
			));
		}
	}
	
	
}