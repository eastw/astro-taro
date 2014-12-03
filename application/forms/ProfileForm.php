<?php
class Application_Form_ProfileForm extends Zend_Form{
	
	public function init(){
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/profile/profile-form.phtml')),
		));
		
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		
		$decorators = array(
			'ViewHelper',
		);
		
		$pass = new Zend_Form_Element_Password('pass');
		$pass->setLabel('Пароль')
				->addFilter('StringTrim')
				->setAttrib('class','reg_form_mini')
				->setDecorators($decorators);
				
		$pass_confirm = new Zend_Form_Element_Password('pass_confirm');
		$pass_confirm->setLabel('Подтверждение пароля')
				->addFilter('StringTrim')
				//->addValidator(new Application_Form_Validation_PasswordConfirmation())
				->setAttrib('class','reg_form_mini')
				->setDecorators($decorators);
		
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Эл. почта')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setAttrib('class','reg_form_mini')
			//->setRequired(true)
			->setValidators(array('NotEmpty','EmailAddress'))
			//new Application_Form_Validation_ExistEmail()
			->setDecorators($decorators);
		
		$fname = new Zend_Form_Element_Text('fname');
		$fname	->addFilter('StripTags')
				->addFilter('StringTrim')
				->setAttrib('class','reg_form_mini')
				->setValidators(array('NotEmpty'))
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				));
		
		$mname = new Zend_Form_Element_Text('mname');
		$mname	->addFilter('StripTags')
				->addFilter('StringTrim')
				->setAttrib('class','reg_form_mini')
				->setValidators(array('NotEmpty'))
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				));
		
		$lname = new Zend_Form_Element_Text('lname');
		$lname	->addFilter('StripTags')
				->addFilter('StringTrim')
				->setAttrib('class','reg_form_mini')
				->addValidator('NotEmpty')
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				))->setAttrib('style', 'width:90%');
		
		$day = new Zend_Form_Element_Select('bday');
		$day//->setRequired(true)
			->setValidators(array('NotEmpty'))
			->setRegisterInArrayValidator(false)
			->setDecorators(array(
					'ViewHelper',
					'Errors',
				))
			->addMultioptions(array('' => 'день'));
		
		$month = new Zend_Form_Element_Select('bmonth');
		$month//->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				));
		
		$year = new Zend_Form_Element_Select('byear');
		$year//->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				));
		
		$gender = new Zend_Form_Element_Select('gender');
		$gender->setLabel('Пол')
				->setValidators(array('NotEmpty'))
				//->setRequired(true)
				->setDecorators($decorators)
				->addMultioptions(array(	
					'' => '...',
					'm' => 'Мужской',
					'f' => 'Женский',
				));
		/*
		$nik = new Zend_Form_Element_Text('nik');
		$nik->addFilter('StripTags')
				->addFilter('StringTrim')
				->setAttrib('class','reg_form_mini')
				->setValidators(array('NotEmpty'))
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				));

		$signature = new Zend_Form_Element_Textarea('signature');
		$signature->addFilter('StripTags')
				->addFilter('StringTrim')
				->setDecorators(array(
						'ViewHelper',
						'Errors',
				));
		*/		
		
		$submit = new Zend_Form_Element_Button('submit');
		$submit->setLabel('Обновить данные');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$this->addElements(array($pass,$pass_confirm,$email,$fname,$mname,$lname,$year,$month,$day,$gender,/*$nik,$signature,*/$submit));
		$this->fillMonthes();
		$this->fillYears();
	}
	
	private function fillYears(){
		$years = array('' => 'год');
		for($i = date('Y'); $i > 1930; $i--){
			$years[$i] = $i;
		}
		$this->byear->addMultiOptions($years);
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
	}
}