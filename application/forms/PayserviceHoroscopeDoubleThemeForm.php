<?php
class Application_Form_PayserviceHoroscopeDoubleThemeForm extends Zend_Form{
	
	protected $_positions;
	
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/forms/payservice-horoscope-double-theme-form.phtml')),
		));
		
		//$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		//$this->setIsArray(true);
		
		$this->setName('theme');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
		);
		
		$name1 = new Zend_Form_Element_Text('name1');
		$name1
			->setDecorators($decorators)
			->setValidators(array('NotEmpty'))
			->setRequired(true)
			->setAttrib('class', 'reg_form');
			
		$year1 = new Zend_Form_Element_Select('year1');
		$year1->setDecorators($decorators)
					->setRequired(true);
		$month1 = new Zend_Form_Element_Select('month1');
		$month1->setDecorators($decorators)
					->setRequired(true);
		$day1 = new Zend_Form_Element_Select('day1');
		$day1->setDecorators($decorators)
				->setRegisterInArrayValidator(false)
					->setRequired(true);
					
		$city1 = new Zend_Form_Element_Text('city1');
		$city1
			->setDecorators($decorators)
			->setValidators(array('NotEmpty'))
			->setRequired(true)
			->setAttrib('class', 'reg_form')
			->setAttrib('placeholder', 'Город, Страна');

		$hour1 = new Zend_Form_Element_Select('hour1');
		$hour1->setDecorators($decorators)
					->setRequired(true);

		$minute1 = new Zend_Form_Element_Select('minute1');
		$minute1->setDecorators($decorators)
					->setRequired(true);

		$this->addElement(
			'checkbox', 'dontknow1', array(
					'class' => 'idk',
					'uncheckedValue' => '',
					'checkedValue' => '1'
			)
		);			
		$this->dontknow1->addValidator('NotEmpty');
		
		$this->dontknow1->setDecorators(array(
				'ViewHelper',
		));
		
		
		$name2 = new Zend_Form_Element_Text('name2');
		$name2
			->setDecorators($decorators)
			->setValidators(array('NotEmpty'))
			->setRequired(true)
			->setAttrib('class', 'reg_form');
			
		$year2 = new Zend_Form_Element_Select('year2');
		$year2->setDecorators($decorators)
					->setRequired(true);
		$month2 = new Zend_Form_Element_Select('month2');
		$month2->setDecorators($decorators)
					->setRequired(true);
		$day2 = new Zend_Form_Element_Select('day2');
		$day2->setDecorators($decorators)
					->setRequired(true);
					
		$city2 = new Zend_Form_Element_Text('city2');
		$city2
			->setDecorators($decorators)
			->setValidators(array('NotEmpty'))
			->setRequired(true)
			->setAttrib('class', 'reg_form')
			->setAttrib('placeholder', 'Город, Страна');

		$hour2 = new Zend_Form_Element_Select('hour2');
		$hour2->setDecorators($decorators)
					->setRequired(true);

		$minute2 = new Zend_Form_Element_Select('minute2');
		$minute2->setDecorators($decorators)
					->setRequired(true);

		$this->addElement(
			'checkbox', 'dontknow2', array(
					'class' => 'idk',
					'uncheckedValue' => '',
					'checkedValue' => '1'
			)
		);			
		$this->dontknow2->addValidator('NotEmpty');
		
		$this->dontknow2->setDecorators(array(
				'ViewHelper',
		));
					
					
		$email = new Zend_Form_Element_Text('email');
		$email
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty','EmailAddress'))
			->setAttrib('class','reg_form')
			->setAttrib('style', 'margin-bottom:5px')
			->setDecorators($decorators);
			
		$detail = new Zend_Form_Element_Textarea('detail');
		$detail
			->setDecorators($decorators)
			->setValidators(array('NotEmpty'))
			->setAttrib('class', 'primechanie')
			->setAttrib('placeholder', 'Ваш комментарий')
			->setAttrib('style', 'height:100px;')
			->setRequired(true);
			
		$paymentType = new Zend_Form_Element_Select('payment_type');
		$paymentType->setDecorators($decorators)
					->setRegisterInArrayValidator(false)
					->setRequired(true);
					
		$formType = new Zend_Form_Element_Hidden('form_type');
		$formType->setDecorators($decorators)->setValue('horoscope');
		
		$partner = new Zend_Form_Element_Hidden('partner');
		$partner->setDecorators($decorators)->setValue('y');			
		
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
		
		$this->addElements(array($name1,$year1,$month1,$day1,$city1,$hour1,$minute1, $name2,$year2,$month2,$day2,$city2,$hour2,$minute2,$email,$detail,$paymentType,$formType,$partner,$alias,$summ,$submit));
		
		$this->fillMonthes();
		$this->fillYears();
		$this->fillHours();
		$this->fillMinutes();
		$this->fillDays();
	}
	
	private function fillYears(){
		$years = array();
		for($i = date('Y'); $i > 1930; $i--){
			$years[$i] = $i;
		}
		$this->year1->addMultiOptions($years);
		$this->year2->addMultiOptions($years);
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
		$this->month1->addMultiOptions($monthes);
		$this->month2->addMultiOptions($monthes);
	}
	
	public function fillDays($year=NULL,$month=NULL){
		if(!$year){
			$year = date('Y');
		}
		if(!$month){
			$month = date('m');
		}
		//
		$num = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 31
		$days = array();
		for($i = 1; $i < ($num + 1); $i++){
			$days[$i] = $i;
		}
		$this->day1->setMultiOptions($days);
		
		$year = date('Y');
		$month = date('m');
		$num = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 31
		$days = array();
		for($i = 1; $i < ($num + 1); $i++){
			$days[$i] = $i;
		}
		$this->day2->setMultiOptions($days);
	}
	
	public function fillPayGates($data){
		$paymentType = $this->getElement('payment_type');
		foreach($data as $item){
			$paymentType->addMultiOptions(array(
				$item['gate'] => $item['gate']
			));
		}
	}
	
	private function fillHours(){
		$hour1 = $this->getElement('hour1');
		$hour2 = $this->getElement('hour2');
		for($i=0,$n=24; $i < $n;$i++):
			if($i < 10){
				$hour1->addMultiOptions(array(
					'0'.$i => '0'.$i 
				));
				$hour2->addMultiOptions(array(
					'0'.$i => '0'.$i 
				));
			}else{
				$hour1->addMultiOptions(array(
					$i => $i 
				));
				$hour2->addMultiOptions(array(
					$i => $i 
				));
			}
        endfor;
	}
	private function fillMinutes(){
		$minute1 = $this->getElement('minute1');
		$minute2 = $this->getElement('minute2');
		for($i=0,$n=60; $i < $n;$i++):
			if($i < 10){
				$minute1->addMultiOptions(array(
					'0'.$i => '0'.$i 
				));
				$minute2->addMultiOptions(array(
					'0'.$i => '0'.$i 
				));
			}else{
				$minute1->addMultiOptions(array(
					$i => $i 
				));
				$minute2->addMultiOptions(array(
					$i => $i 
				));
			}
        endfor;
	}
}