<?php
class Application_Form_KarmaForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/forms/karma-form.phtml')),
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
		);
		
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
		
		$submit = new Zend_Form_Element_Button('submit');
		$submit->setLabel('Отправить данные');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$this->addElements(array($year,$month,$day,$submit));
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