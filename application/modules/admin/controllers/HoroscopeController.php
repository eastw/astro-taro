<?php
class Admin_HoroscopeController extends Zend_Controller_Action{
	protected $service;
	
	public function preDispatch(){
		$this->service = new App_HoroscopeService();
	}
	
	public function simpleAction(){
		$this->view->sunSigns = $this->service->getSunSigns();
		$this->view->keltSigns = $this->service->getKeltSigns();
		$this->view->chinaSigns = $this->service->getChinaSigns();
		$this->view->signTypes = $this->service->getSignTypes();
		$this->view->chinaTypes = $this->service->getChinaTypes();
	}
	
	public function getSimpleSignAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$sign = $this->_getParam('sign',false);
		$sign_type = $this->_getParam('sign_type',false);
		if($sign && $sign_type){
			$chinaType = null;
			if($sign_type == App_HoroscopeService::HOROSCOPE_SIGN_TYPE_CHINA){
				$chinaType = $this->_getParam('china_type',false);
			}
			echo Zend_Json::encode($this->service->getSign($sign_type,$sign,$chinaType));
		}
	}
	
	public function saveSimpleSignAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$sign = $this->_getParam('sign',false);
		$signtype = $this->_getParam('signtype',false);
		$description = $this->_getParam('description','');
		//var_dump($description); die;
		$chinaType = $this->_getParam('china_type',false);
		if($sign && $signtype){
			$this->service->saveSign($sign,$signtype,$description,$chinaType);
		}
	}
	
	public function byTypeAction(){
		$this->view->types = $this->service->getHoroscopeByTypeTypes();
		$this->view->signs = $this->service->getSunSigns();
		
	}
	
	public function getHoroscopeByTypeAndSignAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$sign = $this->_getParam('sign',false);
		$type = $this->_getParam('type',false);
		if($sign && $type){
			$item = $this->service->getHoroscopeByTypeAndSign($type, $sign); 
			echo Zend_Json::encode($item);
		}
		
	}
	
	public function saveByTypeAndSignAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$sign = $this->_getParam('sign',false);
		$type = $this->_getParam('type',false);
		$description = $this->_getParam('description','');
		if($sign && $type){
			$item = $this->service->updateHoroscopeByTypeAndSign($type, $sign,$description);
		}
	}
	
	public function byTimeAction(){
		$this->view->signs = $this->service->getSunSigns();
	}
	
	public function getDataByTimeAction(){
		$this->_helper->layout->disableLayout();
		$type = $this->_getParam('type',false);
		$next = $this->_getParam('next',false);
		//var_dump($next); die;
		if($type){
			$this->prepareData($type,$next);
			switch ($type){
				case 'today': $this->render('time/today'); break;
				case 'week': $this->render('time/week'); break;
				case 'month': $this->render('time/month'); break;
				case 'year': $this->render('time/year'); break;
			}
		}
	}
	
	protected function prepareData($type,$next){
		switch ($type){
			case 'today':
				 if($next){
				 	$this->view->monthDays = date('t',strtotime('+1 month'));
				 	$this->view->curMonth = $this->service->russianMonth(date('m',strtotime('+1 month')));
				 	$this->view->curMonthNumber = date('m',strtotime('+1 month'));
				 	$this->view->todayDay = -1;
				 	$this->view->year = date('Y',strtotime('+1 month'));
				 	$this->view->isNext = true;
				 }else{
				 	$this->view->monthDays = date("t", strtotime(date('Y-m')));
				 	$this->view->todayDay = date('d');
				 	$this->view->curMonth = $this->service->russianMonth(date('m'));
				 	$this->view->curMonthNumber = date('m');
				 	$this->view->year = date('Y');
				 	$this->view->isNext = false;
				 }
				 break;
			case 'week':
				 $weeks = array();
				 $startdate = date('Y-m-d',strtotime('last monday'));
				 for($i = 0; $i < 10;$i++){
				 	$tmp = array();
				 	$tmp['startdate'] = date('Y-m-d',strtotime($startdate.' +'.$i.' week'));
				 	$tmp['enddate'] = date('Y-m-d',strtotime($startdate.'+'.($i+1).' week -1 day'));
				 	$weeks[] = $tmp;
				 }
				 $this->view->weeks = $weeks;
				 break;
			case 'month': 
				$months = array();
				$startdate = date('Y-m').'-01';
				for($i = 0; $i < 10;$i++){
					$tmp = array();
					$tmp['startdate'] = date('Y-m-d',strtotime($startdate.' +'.$i.' month'));
					$tmp['enddate'] = date('Y-m-d',strtotime($startdate.' +'.($i+1).' month -1 day'));
					$months[] = $tmp;
				}
				$this->view->months = $months;
				break;
			case 'year': 
				$years = array();
				$startdate = date('Y').'-01-01';
				for($i = 0; $i < 10;$i++){
					$tmp = array();
					$tmp['startdate'] = date('Y-m-d',strtotime($startdate.' +'.$i.' year'));
					$tmp['enddate'] = date('Y-m-d',strtotime($startdate.' +'.($i+1).' year -1 day'));
					$years[] = $tmp;
				}
				$this->view->years = $years;
				break;
		}
	}
	
	public function getTimeHoroscopeItemAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$type = $this->_getParam('type',false);
		$sign = $this->_getParam('sign',false);
		if($type && $sign){
			$json = array();
			$startdate = $this->_getParam('startdate',false);
			$enddate = $this->_getParam('enddate',false);
			$json = $this->service->getTimeHoroscopeItem($type,$startdate,$enddate,$sign);
			echo Zend_Json::encode($json);
		}
	}
	
	public function saveTimeHoroscopeItemAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$type = $this->_getParam('type',false);
		$sign = $this->_getParam('sign',false);
		if($type && $sign){
			$json = array();
			$startdate = $this->_getParam('startdate',false);
			$enddate = $this->_getParam('enddate',false);
			$description = $this->_getParam('description','');
			$json = $this->service->saveTimeHoroscopeItem($type,$startdate,$enddate,$sign,$description);
		}
	}
	
	public function byCompabilityAction(){
		$this->view->signs = $this->service->getSunSigns();
		$this->view->compabilityTypes = $this->service->getCompabilityTypes();
		//var_dump($this->view->compabilityTypes); die;
	}
	
	public function getCompabilityItemAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$compability = $this->_getParam('compability',false);
		$mainsign = $this->_getParam('mainsign',false);
		$nestedsign = $this->_getParam('nestedsign',false);
		//$maingender = $this->_getParam('maingender',false);
		//$nestedgender = $this->_getParam('nestedgender',false);
		if($compability && $mainsign && $nestedsign /*&& $maingender && $nestedgender*/){
			$json = $this->service->getCompabilityItem($compability, $mainsign, $nestedsign/*, $maingender, $nestedgender*/);
			//var_dump($json); die;
			echo Zend_Json::encode($json); 
		}
	}
	
	public function saveCompabilityItemAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$compability = $this->_getParam('compability',false);
		$mainsign = $this->_getParam('mainsign',false);
		$nestedsign = $this->_getParam('nestedsign',false);
		//$maingender = $this->_getParam('maingender',false);
		//$nestedgender = $this->_getParam('nestedgender',false);
		$description = $this->_getParam('description','');
		if($compability && $mainsign && $nestedsign /*&& $maingender && $nestedgender*/){
			$compTypes = $this->service->getCompabilityTypes();
			foreach($compTypes as $type){
				if($type['id'] == $compability){
					$attributes = $this->service->getCompabilityTypeAttributes($type['id']);
					//var_dump($attributes); die;
					foreach($attributes as &$attribute){
						$attribute['value'] = $this->_getParam($attribute['name'],false);
					}
					$this->service->saveCompabilityItem($compability, $mainsign, $nestedsign, /*$maingender, $nestedgender,*/$description,$attributes);
				}
			}
			//$this->service->getCompabilityItem($compability, $mainsign, $nestedsign, $maingender, $nestedgender);
		}
	}
	
	public function byKarmaAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->service->getKarmaQuery();
		//var_dump($query->assemble()); die;
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(15);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	public function byKarmaAddAction(){
		//$this->view->signs = $this->service->getSunSigns();
		$this->view->actionType = 'add';
		$form = new Application_Form_HoroscopeKarmaPeriodForm();
		$this->view->form = $form;
		$form->fillSigns($this->service->getSunSigns());
		//$form->startdate->setValue('1997-02-01');
		if($this->getRequest()->isPost()){
			$data = $this->_getAllParams();
			if($form->isValid($data)){
				$this->service->addKarmaPeriod($data);
				$this->redirect('/admin/horoscope/by-karma');
			}else{
				$form->populate($data);
			}
		}else{
			//set nearest start date here 
			$period = $this->service->getLastKarmaPeriod();
			$form->startdate->setValue(date('Y-m-d',strtotime($period['enddate'].' +1 day')));
			$form->enddate->setValue(date('Y-m-d',strtotime($period['enddate'].' +1 day')));
		}
		$this->render('by-karma-edit');
	}
	public function byKarmaEditAction(){
		$this->view->signs = $this->service->getSunSigns();
		$this->view->actionType = 'edit';
		
		$id = $this->_getParam('id',false);
		$page = $this->_getParam('page',false);
		
		$period = $this->service->getKarmaPeriodById($id);
		$form = new Application_Form_HoroscopeKarmaPeriodForm();
		$this->view->form = $form;
		$form->fillSigns($this->service->getSunSigns());
		$form->sign->setValue($period['sign_id']);
		$form->is_retrograd->setValue($period['is_retrograd']);
		$form->desc->setValue($period['description']);
		$form->startdate->setValue($period['startdate']);
		$form->enddate->setValue($period['enddate']);
		//$form->startdate->setValue('1997-02-01');
		if($this->getRequest()->isPost()){
			$data = $this->_getAllParams();
			if($form->isValid($data)){
				$this->service->updateKarmaPeriod($data,$period['id']);
				$this->redirect((!empty($page))?'/admin/horoscope/by-karma/page/'.$page :'/admin/horoscope/by-karma');
			}else{
				$form->populate($data);
			}
		}
		
	}
	
	public function removeKarmaPeriodAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		$page = $this->_getParam('page',false);
		$this->service->removeKarmaPeriod($id);
		$this->redirect((!empty($page))?'/admin/horoscope/by-karma/page/'.$page :'/admin/horoscope/by-karma');
	}
	
	public function listPagesAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->service->listPagesQuery();
		
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(25);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	public function addPageAction(){
		$form = new Application_Form_HoroscopePageForm();
		$this->view->form = $form;
		$this->view->actionType = 'add';
		
		//var_dump($this->getRequest()->getRequestUri()); die;
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$this->service->addPage($form->getValidValues($formData));
				$this->redirect('/admin/horoscope/list-pages');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit');
	}
	public function editPageAction(){
		$form = new Application_Form_HoroscopePageForm();
		$this->view->form = $form;
		$this->view->actionType = 'edit';
		
		$id = $this->_getParam('id',false);
		$pageData = $this->service->getPageById($id);
		
		
		$form->page_type->setValue($pageData['horoscope_type']);
		$form->name_ru->setValue($pageData['name_ru']);
		$form->title->setValue($pageData['title']);
		$form->seokeywords->setValue($pageData['keywords']);
		$form->seodescription->setValue($pageData['description']);
		$form->minidesc->setValue($pageData['minidesc']);
		
		$page = $this->_getParam('page','');
		$form->getElement('page')->setValue($page);
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$validData = $form->getValidValues($formData);
				$this->service->savePage($validData,$pageData['id']);
				//$this->redirect((!empty($page))?'/admin/horoscope/index/page/'.$page :'/admin/horoscope/list-pages');
				$this->redirect('/admin/horoscope/list-pages');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit');
	}
	public function removePageAction(){
		$id = $this->_getParam('id',false);
		if(null !== $id){
			$article = $this->service->getPageById($id);
			$this->service->deletePage($id);
			//$this->redirect((!empty($page))?'/admin/horoscope/list-pages/page/'.$page :'/admin/pages');
			$this->redirect('/admin/horoscope/list-pages');
		}
	}
	
	public function searchAction(){
		$this->_helper->layout->disableLayout();
		$query = $this->_getParam('query','');
		$data = $this->service->searchPage($query);
		$this->view->data = $data;
		$this->render('search-page');
	}
}