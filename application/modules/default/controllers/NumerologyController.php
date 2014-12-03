<?php
class NumerologyController extends App_Controller_Action_ParentController{
	
	protected $service;
	
	public function init(){
		$this->service = new App_NumerologyService();
	}
	
	public function indexAction(){
		$bigtype = $this->_getParam('bigtype',false);
		$smalltype = $this->_getParam('smalltype',false);
		
		$smalltypes = $this->service->getSmallNumerologyTypes();
		$bigtypes = $this->service->getBigNumerologyTypes();
		$this->view->fullTypes = $this->service->getFullNumerologyTypes();
		$this->view->topMenuActiveItem = 'numerology';
		//$this->view->smallTypes = $smalltypes;
		//$this->view->bigtypesTypes = $bigtypes;
		//var_dump($bigtype); die;
		if(in_array($smalltype, $smalltypes) && in_array($bigtype,$bigtypes)){
			$this->view->curType = $bigtype;
			$this->view->smallType = $smalltype;
			if($bigtype == 'list'){
				$this->view->sliderExist = true;
				$navItem = $this->view->navigation()->findOneById('numerology-'.$bigtype);
				if($navItem){
					$navItem->setActive('true');
				}
				$this->view->pageTitle = 'Нумерология';
				$this->render($bigtype);
			}elseif($smalltype == 'no-smalltype'){
				$navItem = $this->view->navigation()->findOneById('numerology-'.$bigtype);
				if($navItem){
					$navItem->setActive('true');
				}
				//var_dump($this->view->fullTypes[$bigtype]); die;
				$this->view->pageTitle = $this->view->fullTypes[$bigtype]['name']; 
				$this->render('bigtype');
			}else{
				$navItem = $this->view->navigation()->findOneById('numerology-'.$bigtype.'-'.$smalltype);
				if($navItem){
					$navItem->setActive('true');
				}
				$this->view->smalltype = $smalltype;
				$this->view->numerologyTitle = $this->view->fullTypes[$bigtype]['children'][$smalltype];
				$this->view->pageTitle = $this->view->fullTypes[$bigtype]['children'][$smalltype];
				
				$this->view->socialDescription = $this->view->minidesc;
				
				if(in_array($smalltype,
						array('lifepath','self-expression','identity','soul','achievement','karma')) ){
					$this->preparePersonalData();
				}
				if(in_array($smalltype,
						array('year','month','day')) ){
					$this->prepareForecastData();
				}
				if(in_array($smalltype,
						array('love','partner')) ){
					
					$this->prepareCompabilityData();
				}
				$this->view->attributes = array(
					'type' => 'numerology',
					'subtype' => $smalltype,
					'sign' => '',
					'resource_id' => ''
				);
				$this->view->comments = $this->commentsService->getComments('numerology', $smalltype, '', '');
				$this->render($bigtype);
			}
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	protected function preparePersonalData(){
		//var_dump($this->view->userdata); die;
		$form = new Application_Form_NumerologyForm();
		$form->setUserdata($this->view->userdata);
		$form->startform();
		$form->smalltype->setValue($this->view->smalltype);
		if(isset($this->view->userdata) && !empty($this->view->userdata->fullname)){
			$names = explode(':',$this->view->userdata->fullname);
			//var_dump($this->view->userdata->fullname); die;
			$form->getElement('fname')->setValue($names[0]);
			$form->mname->setValue($names[1]);
			$form->lname->setValue($names[2]);
			
			if(!empty($this->view->userdata->birthday)){
				$date = new Zend_Date($this->view->userdata->birthday);
				$form->byear->setValue($date->get(Zend_Date::YEAR));
				$form->bmonth->setValue($date->get(Zend_Date::MONTH));
				$num = cal_days_in_month(CAL_GREGORIAN, $date->get(Zend_Date::MONTH), $date->get(Zend_Date::YEAR)); // 31
				$days = array();
				for($i = 1; $i < ($num + 1); $i++){
					$days[$i] = $i;
				}
				$form->bday->addMultiOptions($days);
				//$this->view->day = $date->get(Zend_Date::DAY);
				$form->bday->setValue($date->get(Zend_Date::DAY));
			}
		}
		$this->view->form = $form;
	}
	
	protected function prepareForecastData(){
		$form = new Application_Form_NumerologyForecastForm();
		$form->setUserdata($this->view->userdata);
		$form->startform();
		$form->smalltype->setValue($this->view->smalltype);
		if(isset($this->view->userdata) && !empty($this->view->userdata->fullname)){
			$names = explode(':',$this->view->userdata->fullname);
			$form->getElement('fname')->setValue($names[0]);
			$form->mname->setValue($names[1]);
			$form->lname->setValue($names[2]);
				
			if(!empty($this->view->userdata->birthday)){
				$date = new Zend_Date($this->view->userdata->birthday);
				$form->byear->setValue($date->get(Zend_Date::YEAR));
				$form->bmonth->setValue($date->get(Zend_Date::MONTH));
				$num = cal_days_in_month(CAL_GREGORIAN, $date->get(Zend_Date::MONTH), $date->get(Zend_Date::YEAR)); // 31
				$days = array();
				for($i = 1; $i < ($num + 1); $i++){
					$days[$i] = $i;
				}
				$form->bday->addMultiOptions($days);
				$form->bday->setValue($date->get(Zend_Date::DAY));
			}
		}
		$date = new Zend_Date(date('Y-m-d'));
		$form->pyear->setValue($date->get(Zend_Date::YEAR));
		$form->pmonth->setValue($date->get(Zend_Date::MONTH));
		$num = cal_days_in_month(CAL_GREGORIAN, $date->get(Zend_Date::MONTH), $date->get(Zend_Date::YEAR)); // 31
		$days = array();
		for($i = 1; $i < ($num + 1); $i++){
			$days[$i] = $i;
		}
		$form->pday->addMultiOptions($days);
		$form->pday->setValue($date->get(Zend_Date::DAY));
		
		$this->view->form = $form;
	}
	
	protected function prepareCompabilityData(){
		$form = new Application_Form_NumerologyCompabilityForm();
		$form->setUserdata($this->view->userdata);
		$form->setFormType($this->view->smalltype);
		$form->startform();
		$form->smalltype->setValue($this->view->smalltype);
		if(isset($this->view->userdata) && !empty($this->view->userdata->fullname)){
			$names = explode(':',$this->view->userdata->fullname);
			$form->getElement('fname1')->setValue($names[0]);
			$form->mname1->setValue($names[1]);
			$form->lname1->setValue($names[2]);
		
			if(!empty($this->view->userdata->birthday)){
				$date = new Zend_Date($this->view->userdata->birthday);
				$form->byear1->setValue($date->get(Zend_Date::YEAR));
				$form->bmonth1->setValue($date->get(Zend_Date::MONTH));
				$num = cal_days_in_month(CAL_GREGORIAN, $date->get(Zend_Date::MONTH), $date->get(Zend_Date::YEAR)); // 31
				$days = array();
				for($i = 1; $i < ($num + 1); $i++){
					$days[$i] = $i;
				}
				$form->bday1->addMultiOptions($days);
				$form->bday1->setValue($date->get(Zend_Date::DAY));
			}
		}
		$this->view->form = $form;
	}
	
	public function getDescriptionAction(){
		$this->_helper->layout->disableLayout();
		//$this->_helper->viewRenderer->setNoRender();
		
		$type = $this->_getParam('smalltype',false);
		if($type ){
			if(in_array($type,
					array('lifepath','self-expression','identity','soul','achievement','karma'))){
				$fname = $this->_getParam('fname',false);
				$mname = $this->_getParam('mname',false);
				$lname = $this->_getParam('lname',false);
				
				$byear = $this->_getParam('byear',false);
				$bmonth = $this->_getParam('bmonth',false);
				$bday = $this->_getParam('bday',false);
				
				//if(){
				//$data = $this->service->getPersonalNumberByBirthdayAndName($byear.'-'.$bmonth.'-'.$bday, $fname.' '.$mname.' '.$lname, $type);
				$this->view->data = $this->service->getPersonalNumberByBirthdayAndName($byear.'-'.$bmonth.'-'.$bday, $fname.' '.$mname.' '.$lname, $type);
				$this->render('description/'.$type);
				//echo Zend_Json::encode($data);//var_dump($data); die;
				//exit;
				//}
			}
			if(in_array($type,
					array('year','month','day')) ){
				
				$fname = $this->_getParam('fname',false);
				$mname = $this->_getParam('mname',false);
				$lname = $this->_getParam('lname',false);
				
				$byear = $this->_getParam('byear',false);
				$bmonth = $this->_getParam('bmonth',false);
				$bday = $this->_getParam('bday',false);
				
				$pyear = $this->_getParam('pyear',false);
				$pmonth = $this->_getParam('pmonth',false);
				$pday = $this->_getParam('pday',false);
				
				$this->view->data = $this->service->getForecastByBirthdayAndDate($byear.'-'.$bmonth.'-'.$bday,$pyear.'-'.$pmonth.'-'.$pday , $type);
				//var_dump($this->view->data); die;
				$this->render('description/'.$type);
				//exit;
			}
			if(in_array($type,
						array('love','partner')) ){
				$fname1 = $this->_getParam('fname1',false);
				$mname1 = $this->_getParam('mname1',false);
				$lname1 = $this->_getParam('lname1',false);
				
				$byear1 = $this->_getParam('byear1',false);
				$bmonth1 = $this->_getParam('bmonth1',false);
				$bday1 = $this->_getParam('bday1',false);
				
				$fname2 = $this->_getParam('fname2',false);
				$mname2 = $this->_getParam('mname2',false);
				$lname2 = $this->_getParam('lname2',false);
				
				$byear2 = $this->_getParam('byear2',false);
				$bmonth2 = $this->_getParam('bmonth2',false);
				$bday2 = $this->_getParam('bday2',false);
				
				$birthday1 = $byear1.'-'.$bmonth1.'-'.$bday1;
				$birthday2 = $byear2.'-'.$bmonth2.'-'.$bday2;
				
				$fullname1 = $fname1.' '.$mname1.' '.$lname1; 
				$fullname2 = $fname2.' '.$mname2.' '.$lname2;
				
				$this->view->smalltype = $type;
				
				$this->view->data = $this->service->getCompabilityData($birthday1,$birthday2,$fullname1,$fullname2,$type);
				$this->render('description/'.$type);
			}
		}
	}
	
	public function dayDescriptionAction(){
		$personalDayNumber = $this->view->navigation()->findOneById('numerology-personal-day');//->setActive('true');
		//var_dump($personalDayNumber); die;
		if($personalDayNumber){
			$personalDayNumber->setActive('true');
		}
		if(isset($this->view->userdata) && !empty($this->view->userdata)){
			if(isset($this->view->userdata->birthday) && isset($this->view->userdata->birthday)){
				$this->view->pageTitle = 'Ваше персональное число дня';
				$this->view->numberDayData = $this->service->calcTodayNumber($this->view->userdata->birthday);
				//var_dump($this->view->numberDayData); die;
			}
		}
		
	}
}
