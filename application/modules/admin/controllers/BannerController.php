<?php
class Admin_BannerController extends Zend_Controller_Action{
	protected $service;
	
	public function preDispatch(){
		$this->service = new App_BannerService();
	}
	
	public function adAction(){
		$query = $this->service->buildBannerQuery(App_BannerService::BANNER_TYPE_ADS);
		
		$this->view->etalonPositions = $this->service->getEtalonBannerPositions();
		
		$this->view->savedPositions = $this->service->getSavedBannersPositions();
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber(1,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function addAdAction(){
		$form = new Application_Form_AdBannerForm();
		$form->initForm();
		$this->view->form = $form;
		$this->view->actionType = 'add';
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			$positions = $formData['position'];
			unset($formData['position']);
			if($formData['type'] == 'order'){
				$form->image->setRequired(true);
				$form->startdate->setRequired(true);
				$form->enddate->setRequired(true);
			}
			//var_dump($formData); die;
			if($form->isValid($formData)){
				//echo 'valid!'; die;
				$validData = $form->getValidValues($formData);
				if($formData['type'] == 'order'){
					$adapter = $form->image->getTransferAdapter();
					foreach ($adapter->getFileInfo() as $file) {
						$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
						$newName = uniqid().'.'.$ext;
						$path = realpath(dirname('.')).
						DIRECTORY_SEPARATOR.
						'files'.
						DIRECTORY_SEPARATOR.
						'ad'.
						DIRECTORY_SEPARATOR.
						$newName;
						//var_dump($path); die;
						$validData = $form->getValidValues($formData);
						$validData['image'] = $newName;
						$adapter->addFilter('Rename', array(
								'target' => $path,
								'overwrite' => true
						));
						$adapter->receive($file['name']);
					}
				}
				$validData['positions'] = $positions;
				$this->service->addAd($validData);
				$this->redirect('/admin/banner/ad');
			}else{
				$form->populate($formData);
				//$form->initForm($positions);
			}
		}
		$this->render('edit-ad');
	}
	
	public function editAdAction(){
		$id = $this->_getParam('id',false);
		if($id){
			$banner = $this->service->getBannerById($id);
			$form = new Application_Form_AdBannerForm();
			$form->initForm($banner['positions']);
			$form->type->setValue($banner['outer_type']);
			if($banner['outer_type'] == 'order'){
				$form->image_note->setValue('<img src="/files/ad/'.$banner['filename'].'"/>'); 
				$form->link->setValue($banner['link']);
				
				$form->startdate->setValue($banner['date_started']);
				$form->enddate->setValue($banner['date_ended']);
			}
			if($banner['outer_type'] == 'partner'){
				$form->code->setValue($banner['code']);
			}
			$form->banner->setValue($banner['banner']);
			$form->through->setValue($banner['through']);
			$this->view->form = $form;
			
			if($this->getRequest()->isPost()){
				$formData = $this->_getAllParams();
				$positions = $formData['position'];
				unset($formData['position']);
				if($form->isValid($formData)){
					$validData = $form->getValidValues($formData);
					if($formData['type'] == 'order'){
						$adapter = $form->image->getTransferAdapter();
						foreach ($adapter->getFileInfo() as $file) {
							if(!empty($file['name'])){
								$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
								$newName = uniqid().'.'.$ext;
								$path = realpath(dirname('.')).
								DIRECTORY_SEPARATOR.
								'files'.
								DIRECTORY_SEPARATOR.
								'ad'.
								DIRECTORY_SEPARATOR.
								$newName;
								$validData['image'] = $newName;
								$adapter->addFilter('Rename', array(
										'target' => $path,
										'overwrite' => true
								));
								$adapter->receive($file['name']);
								$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'ad'.DIRECTORY_SEPARATOR.$banner['filename'];
								if(file_exists($realPath)){
									unlink($realPath);
								}
							}
						}
					}
					if($formData['type'] == 'partner'){
						$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'ad'.DIRECTORY_SEPARATOR.$banner['filename'];
						if(file_exists($realPath)){
							unlink($realPath);
						}
					}
					$validData['positions'] = $positions;
					$this->service->saveAd($validData,$banner['id']);
					$this->redirect('/admin/banner/ad');
				}else{
					$form->populate($formData);
				}
			}
		}
		$this->render('edit-ad');
	}
	
	public function deleteAdAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$id = $this->_getParam('id',false);
		if($id){
			$banner = $this->service->getBannerById($id);
			$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'ad'.DIRECTORY_SEPARATOR.$banner['filename'];
			if(file_exists($realPath)){
				unlink($realPath);
			}
			$this->service->deleteBanner($id);
		}
		$this->redirect('/admin/banner/ad');
	}
	
	public function sliderAction(){
		$page = $this->_getParam('page','');
		$query = $this->service->buildBannerQuery(App_BannerService::BANNER_TYPE_SLIDER);
		
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function addSliderAction(){
		$form = new Application_Form_SliderBannerForm();
		$this->view->form = $form;
		$this->view->actionType = 'add';
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				//$cleanValues = $form->getValidValues($formData);
				$adapter = $form->image->getTransferAdapter();
				foreach ($adapter->getFileInfo() as $file) {
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					$newName = uniqid().'.'.$ext;
					$path = realpath(dirname('.')).
					DIRECTORY_SEPARATOR.
					'files'.
					DIRECTORY_SEPARATOR.
					'slider'.
					DIRECTORY_SEPARATOR.
					$newName;
					//var_dump($path); die;
					$validData = $form->getValidValues($formData);
					$validData['image'] = $newName;
					$adapter->addFilter('Rename', array(
							'target' => $path,
							'overwrite' => true
					));
					$adapter->receive($file['name']);
				}
				$this->service->addSlider($validData);
				$this->redirect('/admin/banner/slider');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit-slider');
	}
	
	public function editSliderAction(){
		$form = new Application_Form_SliderBannerForm();
		$this->view->form = $form;
		$this->view->actionType = 'edit';
		
		$id = $this->_getParam('id',false);
		$banner = $this->service->getBannerById($id);
		
		$form->image_note->setValue('<a href="/files/slider/'.$banner['filename'].'"><img style="width: 200px;" src="/files/slider/'.$banner['filename'].'" /></a>');
		$form->image->setRequired(false);
		$form->link->setValue($banner['link']);
		$form->type->setValue($banner['pay_service']);
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$validData = $form->getValidValues($formData);
				$validData['image'] = $banner['filename'];
				$adapter = $form->image->getTransferAdapter();
				foreach ($adapter->getFileInfo() as $file) {
					if(!empty($file['name'])){
						$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
						$newName = uniqid().'.'.$ext;
						$path = realpath(dirname('.')).
						DIRECTORY_SEPARATOR.
						'files'.
						DIRECTORY_SEPARATOR.
						'slider'.
						DIRECTORY_SEPARATOR.
						$newName;
						
						$validData['image'] = $newName;
						$adapter->addFilter('Rename', array(
								'target' => $path,
								'overwrite' => true
						));
						$adapter->receive($file['name']);
						$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'slider'.DIRECTORY_SEPARATOR.$banner['filename'];
						if(file_exists($realPath)){
							unlink($realPath);
						}
					}
				}
				$this->service->saveSlider($validData,$banner['id']);
				$this->redirect('/admin/banner/slider/');
			}else{
				$form->image_note->setValue('<a href="/files/slider/'.$banner['filename'].'"><img style="width: 200px;" src="/files/slider/'.$banner['filename'].'" /></a>');
				$form->populate($formData);
			}
		}
	}
	
	public function deleteSliderAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$id = $this->_getParam('id',false);
		if($id){
			$banner = $this->service->getBannerById($id);
			$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'slider'.DIRECTORY_SEPARATOR.$banner['filename'];
			if(file_exists($realPath)){
				unlink($realPath);
			}
			$this->service->deleteBanner($id);
			$this->redirect('/admin/banner/slider/');
		}
	}
	
	public function sliderReorderAction(){
		$id = $this->_getParam('id',false);
		$oldOrder = $this->getParam('old_order',false);
		$direction = $this->getParam('direction',false);
		if($id && $oldOrder && $direction){
			$this->service->reorderSlider($id, $oldOrder, $direction);
			$this->redirect('/admin/banner/slider');
		}
	}
	
	
}