<?php
class Admin_MoonController extends Zend_Controller_Action{

	protected $service;
	protected $horoscopeService;
	
	public function preDispatch(){
		$this->service = new App_MoonService();
		$this->horoscopeService = new App_HoroscopeService();
	}
	
	public function indexAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->service->buildDaysQuery();
		
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function addDayAction(){
		$form = new Application_Form_MoonDayForm();
		//var_dump($this->service->getAllPhases()); die;
		$form->fillPhases($this->service->getAllPhases());
		if($this->getRequest()->isPost()){
			$data = $this->_getAllParams();
			//var_dump($data); die;
			if($form->isValid($data)){
				$adapter = $form->image->getTransferAdapter();
				foreach ($adapter->getFileInfo() as $file) {
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					$newName = uniqid().'.'.$ext;
					$path = realpath(dirname('.')).
					DIRECTORY_SEPARATOR.
					'files'.
					DIRECTORY_SEPARATOR.
					'moon-days'.
					DIRECTORY_SEPARATOR.
					$newName;
					//var_dump($path); die;
					$validData = $form->getValidValues($data);
					$validData['image'] = $newName;
					$adapter->addFilter('Rename', array(
							'target' => $path,
							'overwrite' => true
					));
					$adapter->receive($file['name']);
				}
				$this->service->addMoonDay($validData);
				$this->redirect('/admin/moon');
			}else{
				$form->populate($data);
			}
		}
		$this->view->form = $form;
		$this->render('edit-day');
	}
	
	public function editDayAction(){
		$id = $this->_getParam('id',false);
		if($id){
			$day = $this->service->getDayById($id);
			//var_dump($day); die;
			$form = new Application_Form_MoonDayForm();
			$form->fillPhases($this->service->getAllPhases());
			$form->number->setValue($day['day_number']);
			$form->img_note->setValue('<img src="/files/moon-days/'.$day['image'].'" />');
			$form->desc->setValue($day['description']);
			$form->phase->setValue($day['moon_phase_id']);
			
			$this->view->type = 'edit';
			$page = $this->_getParam('page','');
			
			if($this->getRequest()->isPost()){
				$data = $this->_getAllParams();
				if($form->isValid($data)){
					$adapter = $form->image->getTransferAdapter();
					$validData = $form->getValidValues($data);
					foreach ($adapter->getFileInfo() as $file) {
						if(!empty($file['name'])){
							$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
							$newName = uniqid().'.'.$ext;
							$path = realpath(dirname('.')).
							DIRECTORY_SEPARATOR.
							'files'.
							DIRECTORY_SEPARATOR.
							'moon-days'.
							DIRECTORY_SEPARATOR.
							$newName;
							//var_dump($path); die;
							
							$validData['image'] = $newName;
							$adapter->addFilter('Rename', array(
									'target' => $path,
									'overwrite' => true
							));
							$adapter->receive($file['name']);
							$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'moon-days'.DIRECTORY_SEPARATOR.$day['image'];
							if(file_exists($realPath)){
								unlink($realPath);
							}
						}
					}
					$this->service->saveMoonDay($validData,$id);
					//$this->redirect('/admin/moon');
					$this->redirect((!empty($page))?'/admin/moon/index/page/'.$page :'/admin/moon');
				}else{
					$form->populate($data);
				}
			}
		}
		$this->view->form = $form;
		$this->render('edit-day');
	}
	
	public function removeDayAction(){
		$id = $this->_getParam('id',false);
		if($id){
			$this->service->removeDay($id);
			$this->redirect('/admin/moon');
		}
	}
	
	public function dayAttributesAction(){
		$this->view->days = $this->service->getAllMoonDays();
		$this->view->attributes = $this->service->getAllDayAttributes();
	}
	
	public function getDayAttributeAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$day = $this->_getParam('day',false);
		$attribute = $this->_getParam('attribute',false);
		echo Zend_Json::encode($this->service->getDayAttribute($day,$attribute));
		
	}
	public function saveDayAttributeAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$day = $this->_getParam('day',false);
		$attribute = $this->_getParam('attribute',false);
		$rating = $this->_getParam('rating',false);
		$description = $this->_getParam('description',false);
		
		$this->service->saveDayAttribute($day,$attribute,$rating,$description);
	}
	
	public function moonInSignAction(){
		$this->view->signs = $this->horoscopeService->getSunSigns();
		//var_dump($this->view->signs); die;
	}
	
	public function getMoonSignAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$sign = $this->_getParam('sign',false); 
		echo Zend_Json::encode($this->service->getMoonSign($sign));
	}
	
	public function saveMoonSignAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$sign = $this->_getParam('sign',false);
		$description = $this->_getParam('description',false);
		$this->service->saveMoonSign($sign,$description);
	}
	
	public function associateAction(){
		$this->view->signs = $this->horoscopeService->getSunSigns();
		$this->view->days = $this->service->getAllMoonDays();
		$session = new Zend_Session_Namespace('moon-day');
		//var_dump($session->day); die;
		if(isset($session->day)){
			unset($session->day);
		}
	}
	public function getAssociateAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$date = $this->_getParam('date',false);
		$data = $this->service->getAssosiateDay($date);
		$session = new Zend_Session_Namespace('moon-day');
		
		if(!isset($session->day)){
			$session->day = array();
		}
		$session->day = $data;
		echo Zend_Json::encode($data);
	}
	public function addAssociateAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$date = $this->_getParam('date',false);
		$dayid = $this->_getParam('moon-day',false);
		$daystart = $this->_getParam('startday',false);
		$daynumber = $this->_getParam('daynumber',false);
		//$data = $this->service->getAssosiateDay($date);
		
		//var_dump($data); die;
		$session = new Zend_Session_Namespace('moon-day');
		if(!isset($session->day)){
			$session->day = array();
		}
		$days = $session->day;
		if(!empty($days) && isset($days['moonDays'])){
			$exist = false;
			foreach($days['moonDays'] as $day){
				if($day['moon_day_id'] == $dayid){
					$exist = true;
				}
				/*
				if($day['moon_day_id'] == null){
					unset($days['moonDays'][$index]);
				}
				*/
			}
			if(!$exist){
				$moondDay = array('moon_day_id' => $dayid,'day_start' => null,'moon_calendar_id' => $days['id'],'day_number' => $daynumber);
				if($daystart){
					$moondDay['day_start'] = $daystart;
				}
				$days['moonDays'][] = $moondDay;
			}
		}
		$session->day = $days;
		echo Zend_Json::encode($days);
	}
	public function deleteAssociateAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$dayid = $this->_getParam('dayid',false);
		$session = new Zend_Session_Namespace('moon-day');
		if(!isset($session->day)){
			$session->day = array();
		}
		$days = $session->day;
		//var_dump($days); 
		if(!empty($days) && isset($days['moonDays'])){
			foreach($days['moonDays'] as $index => $day){
				if($day['moon_day_id'] == $dayid){
					unset($days['moonDays'][$index]);
					$days['moonDays'] = array_values($days['moonDays']); 
					break;
				}
			}
		}
		//var_dump($days); die; 
		$session->day = $days;//$days;
		echo Zend_Json::encode($days);
	}
	
	public function saveAssociateAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$date = $this->_getParam('date',false);
		//$sign = $this->_getParam('sign',false);
		
		$session = new Zend_Session_Namespace('moon-day');
		$day = $session->day; 
		
		$this->service->saveAssociate($date,$day);
	}
	
	public function phaseAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->service->buildPhaseQuery();
		
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(5);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function addPhaseAction(){
		$form = new Application_Form_MoonPhaseForm();
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$this->service->addPhase($form->getValidValues($formData));
				$this->redirect('/admin/moon/phase');
			}else{
				$form->populate($formData);
			}
		}
		$this->view->form = $form;
		$this->render('edit-phase');
	}
	public function editPhaseAction(){
		$id = $this->_getParam('id');
		$form = new Application_Form_MoonPhaseForm();
		$phase = $this->service->getPhaseById($id);
		$form->name->setValue($phase['phase']);
		$form->short_desc->setValue($phase['short_desc']);
		$form->desc->setValue($phase['description']);
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$this->service->updatePhase($form->getValidValues($formData),$id);
				$this->redirect('/admin/moon/phase');
			}else{
				$form->populate($formData);
			}
		}
		$this->view->form = $form;
		$this->render('edit-phase');
	}
	public function removePhaseAction(){
		$id = $this->_getParam('id',false);
		if($id){
			$this->service->removePhase($id);
			$this->redirect('/admin/moon/phase');
		}
	}
	
	public function signAssociateAction(){
		$this->view->signs = $this->horoscopeService->getSunSigns();
		//$this->view->days = $this->service->getAllMoonDays();
		$session = new Zend_Session_Namespace('moon-in-sign');
		//var_dump($session->day); die;
		if(isset($session->day)){
			unset($session->day);
		}
	}
	public function signGetAssociateAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$date = $this->_getParam('date',false);
		$data = $this->service->getAssosiateSign($date);
		$session = new Zend_Session_Namespace('moon-in-sign');
		
		if(!isset($session->day)){
			$session->day = array();
		}
		$session->day = $data;
		echo Zend_Json::encode($data);
	}
	public function signAddAssociateAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$date = $this->_getParam('date',false);
		$dayid = $this->_getParam('moon-day',false);
		$daystart = $this->_getParam('startday',false);
		$daynumber = $this->_getParam('daynumber',false);
		//$data = $this->service->getAssosiateDay($date);
		
		//var_dump($data); die;
		$session = new Zend_Session_Namespace('moon-in-sign');
		if(!isset($session->day)){
			$session->day = array();
		}
		$days = $session->day;
		if(!empty($days) && isset($days['moonDays'])){
			$exist = false;
			foreach($days['moonDays'] as $day){
				if(isset($day['moon_in_sign_id']) && $day['moon_in_sign_id'] == $dayid){
					$exist = true;
				}
			}
			if(!$exist){
				$moondDay = array('moon_in_sign_id' => $dayid,'signstart' => null,'moon_calendar_id' => $days['id'],'day_number' => $daynumber);
				if($daystart){
					$moondDay['signstart'] = $daystart;
				}
				$days['moonDays'][] = $moondDay;
			}
		}
		$session->day = $days;
		echo Zend_Json::encode($days);
	}
	public function signDeleteAssociateAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$dayid = $this->_getParam('dayid',false);
		$session = new Zend_Session_Namespace('moon-in-sign');
		if(!isset($session->day)){
			$session->day = array();
		}
		$days = $session->day;
		//var_dump($days); 
		if(!empty($days) && isset($days['moonDays'])){
			foreach($days['moonDays'] as $index => $day){
				if($day['moon_in_sign_id'] == $dayid){
					unset($days['moonDays'][$index]);
					$days['moonDays'] = array_values($days['moonDays']); 
					break;
				}
			}
		}
		//var_dump($days); die; 
		$session->day = $days;//$days;
		echo Zend_Json::encode($days);
	}
	
	public function signSaveAssociateAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$date = $this->_getParam('date',false);
		//$sign = $this->_getParam('sign',false);
		
		$session = new Zend_Session_Namespace('moon-in-sign');
		$day = $session->day; 
		
		$this->service->saveSignAssociate($date,$day);
	}
}
