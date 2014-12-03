<?php
class Admin_PayserviceController extends Zend_Controller_Action{
	
	protected $service;
	
	protected $navigationService;
	
	public function preDispatch(){
		$this->service = new App_PayserviceService();
		$this->navigationService = new App_NavigationService();
	}
	public function themesAction(){
		$this->view->data = $this->service->listThemes();
	}
	public function addThemeAction(){
		$form = new Application_Form_PayserviceThemeForm();
		$this->view->form = $form;
		$this->view->actionType = 'add';
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$validData = $form->getValidValues($formData);
				$adapter = $form->image->getTransferAdapter();
				foreach ($adapter->getFileInfo() as $file) {
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					$newName = uniqid().'.'.$ext;
					$path = realpath(dirname('.')).
					DIRECTORY_SEPARATOR.
					'files'.
					DIRECTORY_SEPARATOR.
					'paythemes'.
					DIRECTORY_SEPARATOR.
					$newName;
					//var_dump($path); die;
					//$validData = $form->getValidValues($formData);
					$validData['image'] = $newName;
					$adapter->addFilter('Rename', array(
							'target' => $path,
							'overwrite' => true
					));
					$adapter->receive($file['name']);
				}
				$this->service->addTheme($validData);
				$this->navigationService->refreshNavigation();
				$this->redirect('/admin/payservice/themes');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit-theme');
	}
	
	public function editThemeAction(){
		$form = new Application_Form_PayserviceThemeForm();
		$this->view->form = $form;
		$this->view->actionType = 'edit';
		
		$id = $this->_getParam('id',false);
		$theme = $this->service->getThemeById($id);
		
		$form->img_note->setValue('<img src="/files/paythemes/'.$theme['image'].'" />');
		$form->theme->setValue($theme['theme_name']);
		$form->type->setValue($theme['theme_type']);
		$form->double_form->setValue($theme['double_form']);
		$form->cost->setValue($theme['cost']);
		$form->description->setValue($theme['description']);
		$form->seotitle->setValue($theme['seo-title']);
		$form->seokeywords->setValue($theme['seo-keywords']);
		$form->seodescription->setValue($theme['seo-description']);
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$validData = $form->getValidValues($formData);
				
				$validData['image'] = '';
				$adapter = $form->image->getTransferAdapter();
				foreach ($adapter->getFileInfo() as $file) {
					if(!empty($file['name'])){
						$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
						$newName = uniqid().'.'.$ext;
						$path = realpath(dirname('.')).
						DIRECTORY_SEPARATOR.
						'files'.
						DIRECTORY_SEPARATOR.
						'paythemes'.
						DIRECTORY_SEPARATOR.
						$newName;
						
						$validData['image'] = $newName;
						$adapter->addFilter('Rename', array(
								'target' => $path,
								'overwrite' => true
						));
						$adapter->receive($file['name']);
						$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'paythemes'.DIRECTORY_SEPARATOR.$article['image'];
						if(file_exists($realPath)){
							unlink($realPath);
						}
					}
				}
				
				$this->service->saveTheme($validData,$theme['id']);
				$this->navigationService->refreshNavigation();
				$this->redirect('/admin/payservice/themes');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit-theme');
	}
	
	public function removeThemeAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		if($id){
			$this->service->deleteTheme($id);
			$this->navigationService->refreshNavigation();
			$this->redirect('/admin/payservice/themes');
		}
	}
	
	public function gatesAction(){
		$this->view->data = $this->service->listGates();
	}
	
	public function addGateAction(){
		$form = new Application_Form_PayserviceGateForm();
		$this->view->form = $form;
		$this->view->actionType = 'add';
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$validData = $form->getValidValues($formData);
				$this->service->addGate($validData);
				$this->redirect('/admin/payservice/gates');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit-theme');
	}
	public function editGateAction(){
		$form = new Application_Form_PayserviceGateForm();
		$this->view->form = $form;
		$this->view->actionType = 'edit';
		
		$id = $this->_getParam('id',false);
		$gate = $this->service->getGateById($id);
		
		$form->gate->setValue($gate['gate']);
		$form->details->setValue($gate['details']);
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$validData = $form->getValidValues($formData);
				$this->service->saveGate($validData,$gate['id']);
				$this->redirect('/admin/payservice/gates');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit-theme');
	}
	
	public function removeGateAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		if($id){
			$this->service->deleteGate($id);
			$this->redirect('/admin/payservice/gates');
		}
	}
	
	public function editEmailAction(){
		$form = new Application_Form_PayserviceEmailForm();
		$this->view->form = $form;
		$this->view->actionType = 'edit';
		
		$id = $this->_getParam('id',false);
		$email = $this->service->getEmail($id);
		
		$form->email->setValue($email['email']);
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$validData = $form->getValidValues($formData);
				$this->service->saveEmail($validData,$email['id']);
				$this->redirect('/admin/payservice/edit-email');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit-theme');
	}
	
	//public function 
}