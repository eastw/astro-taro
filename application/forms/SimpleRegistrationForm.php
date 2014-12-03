<?php
class Application_Form_SimpleRegistrationForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/forms/registration-form.phtml')),
		));
		
		$this->setName('article');
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
				->setAttrib('class','reg_form')
				//->addValidator(new Application_Form_Validation_PasswordConfirmation())
				->addValidator(new App_Validate_PasswordConfirmation())
				->setDecorators($decorators);
		
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Эл. почта')
			->addFilter('StringTrim')
			->setRequired(true)
			->setValidators(array('NotEmpty','EmailAddress',new App_Validate_ExistEmail()))
			->setAttrib('class','reg_form')
			->setDecorators($decorators);
		/*
		$publickey = '6LdsrucSAAAAAGwY3j6fssqUXDpPE2iOWEe6Xia6';
		$privatekey = '6LdsrucSAAAAAKGSmqPQx-TYMhXbu3YbvUzOt9uM';
		$recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);
		
		$captcha = new Zend_Form_Element_Captcha('captcha',
				array(
						'captcha'       => 'ReCaptcha',
						'captchaOptions' => array('captcha' => 'ReCaptcha', 'service' => $recaptcha),
						'ignore' => true
				)
		);
		$captcha->setDecorators($decorators);
		*/
		
		$captcha = new App_Form_Element_Captcha('captcha',array(
				'captcha' => array('captcha' => 'Image',
						'name'    => 'myCaptcha',
						'wordLen' => 5,
						'timeout' => 300,
						'font'    => APPLICATION_PATH.'/../fonts/Trajan-Bold.ttf',
						'imgDir'  => APPLICATION_PATH . '/../public/files/captcha/',
						'imgUrl'  => '/files/captcha/',
						'fontSize' => '26',
						'lineNoiseLevel' => '0'
				)
		));
		$captcha->setDecorators(array('Errors'));
		
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
		))->setAttrib('class','check')->getValidator('NotEmpty')->setMessage('Чтобы создать аккаунт Astrotarot, нужно принять наши Условия использования');
		
		$formtype = new Zend_Form_Element_Hidden('formtype');
		$formtype->setDecorators(array(
				'ViewHelper',
		));
		$formtype->setValue('simple');
		
		$submit = new Zend_Form_Element_Button('submit');
		$submit->setLabel('Отправить данные');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$this->addElements(array($pass,$pass_confirm,$email,$captcha,$formtype,$submit));
	}
}