<?php
class Application_Form_PayserviceHoroscopeThemeForm extends Zend_Form{
	
	protected $_positions;
	
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/forms/payservice-horoscope-theme-form.phtml')),
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
					
		$city = new Zend_Form_Element_Text('city');
		$city
			->setDecorators($decorators)
			->setValidators(array('NotEmpty'))
			->setRequired(true)
			->setAttrib('class', 'reg_form')
			->setAttrib('placeholder', 'Город, Страна');

		$hour = new Zend_Form_Element_Select('hour');
		$hour->setDecorators($decorators)
					->setRequired(true);

		$minute = new Zend_Form_Element_Select('minute');
		$minute->setDecorators($decorators)
					->setRequired(true);

		$this->addElement(
			'checkbox', 'dontknow', array(
					'class' => 'idk',
					'uncheckedValue' => '',
					'checkedValue' => '1'
			)
		);
		$this->dontknow->addValidator('NotEmpty');
		
		$this->dontknow->setDecorators(array(
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
		
		$this->addElements(array($name,$year,$month,$day,$city,$hour,$minute,$email,$detail,$paymentType,$formType,$partner,$alias,$summ,$submit));
		
		$this->fillMonthes();
		$this->fillYears();
		$this->fillHours();
		$this->fillMinutes();
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
	
	public function fillPayGates($data){
		$paymentType = $this->getElement('payment_type');
		foreach($data as $item){
			$paymentType->addMultiOptions(array(
				$item['gate'] => $item['gate']
			));
		}
	}
	
	private function fillHours(){
		$hour = $this->getElement('hour');
		for($i=0,$n=24; $i < $n;$i++):
			if($i < 10){
				$hour->addMultiOptions(array(
					'0'.$i => '0'.$i 
				));
			}else{
				$hour->addMultiOptions(array(
					$i => $i 
				));
			}
        endfor;
	}
	private function fillMinutes(){
		$minute = $this->getElement('minute');
		for($i=0,$n=60; $i < $n;$i++):
			if($i < 10){
				$minute->addMultiOptions(array(
					'0'.$i => '0'.$i 
				));
			}else{
				$minute->addMultiOptions(array(
					$i => $i 
				));
			}
        endfor;
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
}