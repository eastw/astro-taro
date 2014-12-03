<?php
class Application_Form_FullRegistrationForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/forms/full-registration-form.phtml')),
		));
		
		$this->setName('registration');
		$this->setMethod('post');
		
		/*
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td','requiredSuffix' => '*')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		*/
		$decorators = array(
				'ViewHelper',
				'Errors',
		);
		/*
		$login = new Zend_Form_Element_Text('login');
		$login->setLabel('Логин')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);
		*/
		
		$pass = new Zend_Form_Element_Password('pass');
		$pass->setLabel('Пароль')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->addValidator(new Zend_Validate_StringLength(
						array(
								'min' => 6,
								'max' => 12)))
				->setAttrib('class','reg_form')
				->setDecorators($decorators);
				
		$pass_confirm = new Zend_Form_Element_Password('pass_confirm');
		$pass_confirm->setLabel('Подтверждение пароля')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->addValidator(new Zend_Validate_StringLength(
						array(
								'min' => 6,
								'max' => 12)))
				->addValidator(new App_Validate_PasswordConfirmation())
				->setAttrib('class','reg_form')
				->setDecorators($decorators);
		
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Эл. почта')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty','EmailAddress',new App_Validate_ExistEmail()))
			->setAttrib('class','reg_form')
			->setDecorators($decorators);
		
		$fname = new Zend_Form_Element_Text('fname');
		$fname	->addFilter('StripTags')
				->setLabel('1')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setAttrib('class','reg_form_mini')
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				))->setValue('Имя');
		
		$mname = new Zend_Form_Element_Text('mname');
		$mname->setLabel('1')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setAttrib('class','reg_form3')
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				))->setValue('Отчество');
		
		$lname = new Zend_Form_Element_Text('lname');
		$lname	->addFilter('StripTags')
				->setLabel('1')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setAttrib('class','reg_form_mini2')
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				))->setValue('Фамилия');
		
		$day = new Zend_Form_Element_Select('bday');
		$day->setValidators(array('NotEmpty'))
			->setRegisterInArrayValidator(false)
			//->setRequired(true)
			->setDecorators(array(
					'ViewHelper',
					'Errors',
				))
			->addMultioptions(array('' => 'день'));
		
		$month = new Zend_Form_Element_Select('bmonth');
		$month->setValidators(array('NotEmpty'))
				//->setRequired(true)
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				));
		
		$year = new Zend_Form_Element_Select('byear');
		$year->setValidators(array('NotEmpty'))
				//->setRequired(true)
				->setDecorators(array(
					'ViewHelper',
					'Errors',
				));
		
		$gender = new Zend_Form_Element_Select('gender');
		$gender->setLabel('Пол')
				->setValidators(array('NotEmpty'))
				->setRequired(true)
				->setDecorators($decorators)
				->addMultioptions(array(	
					'' => '...',
					'm' => 'Мужской',
					'f' => 'Женский',
				));

		/*		
		$publickey = '6LdsrucSAAAAAGwY3j6fssqUXDpPE2iOWEe6Xia6';
		$privatekey = '6LdsrucSAAAAAKGSmqPQx-TYMhXbu3YbvUzOt9uM';
		$recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);
		
		$captcha = new Zend_Form_Element_Captcha('captcha2',
				array(
						'captcha'       => 'ReCaptcha',
						'captchaOptions' => array('captcha' => 'ReCaptcha', 'service' => $recaptcha),
						'ignore' => true
				)
		);
		*/
			
		$captcha = new App_Form_Element_Captcha('full_captcha',array('label'   => "",
                                                                      'captcha' => array('captcha' => 'Image',
                                                                                         'name'    => 'myCaptcha',  
                                                                                         'wordLen' => 5,  
                                                                                         'timeout' => 300,  
                                                                                         'font'    => APPLICATION_PATH.'/../fonts/Trajan-Bold.ttf',  
                                                                                         'imgDir'  => APPLICATION_PATH . '/../public/files/captcha/',  
                                                                                         'imgUrl'  => '/files/captcha/',
                                                                      					'fontSize' => '26',
                                                                      					'lineNoiseLevel' => '3',
                                                                      					//'pointNoiseLevel' => '8'
                                                                      )
                                                                     ));
		//$test = $captcha->getCaptcha();
		//$test->
		//var_dump($test->generate()); die;
		//$captcha->setDecorators(array('captcha', array('ViewScript', array('viewScript' => 'partials/captcha.phtml'))));
		$captcha->setDecorators(array(
			'Errors'
		))->setRequired(true);
		
		$this->addElement(
			'checkbox', 'agree_full', array(
					'required' => true,
					'uncheckedValue' => '',
					'checkedValue' => '1'
			)
		);
		$this->agree_full->addValidator('NotEmpty');
		
		$this->agree_full->setDecorators(array(
				'ViewHelper',
		))->setAttrib('class','check')->getValidator('NotEmpty')->setMessage('Чтобы создать аккаунт Astrotarot, нужно принять наши Условия использования');
		//var_dump($this->agree->getErrorMessages()); die;
		
		$formtype = new Zend_Form_Element_Hidden('formtype');
		$formtype->setDecorators(array(
				'ViewHelper',
		));
		$formtype->setValue('full');
		
		$submit = new Zend_Form_Element_Button('submit');
		$submit->setLabel('Отправить данные');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$this->addElements(array($pass,$pass_confirm,$email,$fname,$mname,$lname,$year,$month,$day,$gender,$captcha,$formtype,$submit));
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

//$num = cal_days_in_month(CAL_GREGORIAN, 8, 2003); // 31
//echo "There was $num days in August 2003";