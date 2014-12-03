<?php

class IndexController extends App_Controller_Action_ParentController
{
	protected $service;
	    
	public function preDispatch() {
		parent::preDispatch();
		$this->service = new App_IndexService();
	}
    
    public function indexAction()
    {
    	$this->view->moonData = $this->service->getTodayMoonData();
    	$this->view->magicData = $this->service->getMagicData();
    	$this->view->articleData = $this->service->getArticleData();
    	$this->view->newsData = $this->service->getNewsData();
    	$this->view->horoscopeData = $this->service->todayHoroscopeData();
    	$this->view->taroDayData = $this->service->taroDay($this->view->taroDay,$this->view->taroDayState);
    	$this->view->runeDayData = $this->service->runeDay($this->view->runeDay,$this->view->runeDayState);
    	$this->view->hexagrammDayData = $this->service->hexagrammDay($this->view->hexagrammDay);
    	
    	//var_dump($this->view->hexagrammDayData); die;
    	if(isset($this->view->userdata) && !empty($this->view->userdata)){
    		if(isset($this->view->userdata->birthday) && !empty($this->view->userdata->birthday)){
    			$this->view->numberDayData = $this->service->numberDayData($this->view->userdata->birthday);
    		}
    	}
    }
    
    /*
    public function serviceAction(){
    	$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$data = $this->_getAllParams();
		$email = $this->payservice->getEmail();
		$this->service->payService($data,$email);
	}
	*/
	
	/*
	public function servicePriceAction(){
    	$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$data = $this->_getAllParams();
		echo Zend_Json::encode($this->payservice->getThemePrice($data));
	}
	*/
	
	public function payserviceCommentsAction(){
		$this->_helper->layout->disableLayout();
		
		$commentService = new App_CommentsService();
		$this->view->comments = $commentService->getPayserviceComments();
	}
	
	public function feedbackAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$data = $this->_getAllParams();
		$form = new Application_Form_FeedbackForm();
		if($form->isValid($data)){
			$mailService = new App_MailService();
			$mailService->sendFeedback($data);
		}
		echo Zend_Json::encode($form->getMessages());
	}

	public function sitemapAction(){
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		echo $this->view->navigation()->sitemap();
	}
	
}