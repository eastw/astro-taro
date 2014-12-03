<?php
class MoonController extends App_Controller_Action_ParentController{
	
	protected $service;
	
	public function init(){
		$this->service = new App_MoonService();
	}
	
	public function todaySmallAction(){
		$date = date('Y-m-d');
		$data = $this->service->getDateSmallData($date);
		/*
		echo '<pre>';
		var_dump($data); 
		die;
		*/
		$this->view->data = $data;
		$this->view->pageTitle = 'Лунный календарь';
		$this->view->sliderExist = true;
		
		$startdate = date('Y-m-d',strtotime(date('Y-m').'-01'.' -3 month'));//date('Y-m').'-01';
		$monthCount = 9;
		$monthArray = array();
		
		for($i=0;$i< $monthCount;$i++){
			$tmp = array(
				'year' => date('Y',strtotime($startdate.' +'.$i.' month')),
				'month'	 => date('m',strtotime($startdate.' +'.$i.' month'))
			);
			$monthArray[] = $tmp;
		}
		$this->view->monthArray = $monthArray;
		$navItem = $this->view->navigation()->findOneById('moon-small');
		if($navItem){
			$navItem->setActive('true');
		}
		$this->view->topMenuActiveItem = 'moon';
		
		$this->view->monthDays = $this->service->getDays(date('m-Y'));
		//var_dump($this->view->monthDays); die;
	}
	
	public function todayDetailAction(){
		$date = date('Y-m-d');
		
		$this->view->tomorrow = date('Y-m-d',strtotime($date.' +1 day'));
		$this->view->yesterday = date('Y-m-d',strtotime($date.' -1 day'));
		
		$data = $this->service->getDateDetailData($date);
		
		/*
		echo '<pre>';
		var_dump($data); 
		die;
		*/
		$this->view->data = $data;
		$this->view->pageTitle = 'Луна сегодня, описание лунного дня';
		
		if(isset($data['moonDays']) && count($data['moonDays'])){
			$this->view->socialDescription = preg_replace('#(<.*?>)#ims','',$data['moonDays'][0]['day_detail']['description']);
		}else{
			$this->view->socialDescription = '';
		}
		
		$startdate = date('Y-m').'-01';
		$monthCount = 2;
		$monthArray = array();
		
		for($i=0;$i< $monthCount;$i++){
			$tmp = array(
					'year' => date('Y',strtotime($startdate.' +'.$i.' month')),
					'month'	 => date('m',strtotime($startdate.' +'.$i.' month'))
			);
			$monthArray[] = $tmp;
		}
		$this->view->monthArray = $monthArray;
		$navItem = $this->view->navigation()->findOneById('moon-full');
		if($navItem){
			$navItem->setActive('true');
		}
		$this->view->topMenuActiveItem = 'moon';
	}
	
	public function getDaysAction(){
		$this->_helper->layout->disableLayout();
		//$this->_helper->viewRenderer->setNoRender();
		$monthYear = $this->_getParam('month-year');
		$this->view->data = $this->service->getDays(preg_replace('/<\/?[^>]+>/ims','',$monthYear));
	}
	
	public function dayAction(){
		$dayparam = $this->_getParam('dayparam');
		$dayparam = preg_replace('/<\/?[^>]+>/ims','',$dayparam);
		
		if(App_UtilsService::isDate($dayparam)){
			$this->view->date = $dayparam;
			$this->view->tomorrow = date('Y-m-d',strtotime($dayparam.' +1 day'));
			$this->view->yesterday = date('Y-m-d',strtotime($dayparam.' -1 day'));
			$this->view->data = $this->service->getDateDetailData($dayparam);
			$navItem = $this->view->navigation()->findOneById('moon-day');
			if($navItem){
				$navItem->setActive('true');
			}
			$this->view->topMenuActiveItem = 'moon';
			$date = new Zend_Date($dayparam);
			$this->view->pageTitle = 'Лунный календарь на '.$date->toString(Zend_Date::DATE_LONG);
			$this->view->seotitle .= ' на '.$date->toString(Zend_Date::DATE_LONG);
		}
	}
	
	public function postDispatch(){
		
	}
} 
