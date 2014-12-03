<?php
class Admin_NumerologyController extends Zend_Controller_Action{

	protected $service;

	public function preDispatch(){
		$this->service = new App_NumerologyService();
	}
	
	public function personalAction(){
		$types = $this->service->getTypes();
		for($i = 0,$n = count($types);$i<$n;$i++){
			if(in_array($types[$i]['id'],array('10','11'))){
				unset($types[$i]);
			}
		}
		$this->view->types = $types;
	}
	
	public function loadPersonalAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if($this->getRequest()->isPost()){
			$number = $this->_getParam('number',false);
			$type = $this->_getParam('type',false);
			$aloop = $this->_getParam('aloop',false);
			echo Zend_Json::encode($this->service->getPersonalNumber($number, $type,$aloop));
		}
	}
	public function savePersonalAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if($this->getRequest()->isPost()){
			$number = $this->_getParam('number',false);
			$type = $this->_getParam('type',false);
			$aloop = $this->_getParam('aloop',false);
			$description = $this->_getParam('description',false);
			$this->service->savePersonalNumber($number, $type,$aloop,$description);
		}
	}
	public function compabilityAction(){
		$types = $this->service->getTypes();
		for($i = 0,$n = count($types);$i<$n;$i++){
			if(!in_array($types[$i]['id'],array('10','11'))){
				unset($types[$i]);
			}
		}
		$this->view->types = $types;
		$this->view->compTypes = $this->service->getCompabilityTypes();
	}
	public function loadCompabilityAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		if($this->getRequest()->isPost()){
			$numberType = $this->_getParam('number_type',false);
			$type = $this->_getParam('type',false);
			$number1 = $this->_getParam('number1',false);
			$number2 = $this->_getParam('number2',false);
			echo Zend_Json::encode($this->service->getCompability($numberType, $type,$number1,$number2));
		}
		
	}
	public function saveCompabilityAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		if($this->getRequest()->isPost()){
			$numberType = $this->_getParam('number_type',false);
			$type = $this->_getParam('type',false);
			$number1 = $this->_getParam('number1',false);
			$number2 = $this->_getParam('number2',false);
			$description = $this->_getParam('description',false);
			$percent = $this->_getParam('percent',false);
			$this->service->saveCompability($numberType, $type,$number1,$number2,$description,$percent);
		}
	}
	
	public function percentAction(){
		$types = $this->service->getTypes();
		for($i = 0,$n = count($types);$i<$n;$i++){
			if(!in_array($types[$i]['id'],array('10','11'))){
				unset($types[$i]);
			}
		}
		$this->view->types = $types;
	}
	
	public function getPercentAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$type = $this->_getParam('type',false);
		$percent = $this->_getParam('percent',false);
		echo Zend_Json::encode($this->service->getPercentDescription($type,$percent));
	}
	public function savePercentAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$type = $this->_getParam('type',false);
		$percent = $this->_getParam('percent',false);
		$description = $this->_getParam('description',false);
		$this->service->savePercentDescription($type,$percent,$description);
	}
}