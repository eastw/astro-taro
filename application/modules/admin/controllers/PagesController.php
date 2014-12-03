<?php
class Admin_PagesController extends Zend_Controller_Action{
	
	protected $service;
	
	public function preDispatch(){
		$this->service = new App_PagesService();
	}
	
	public function indexAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->service->buildPagesQuery();
		
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function addAction(){
		$form = new Application_Form_PageForm();
		$this->view->form = $form;
		$this->view->actionType = 'add';
		
		//var_dump($this->getRequest()->getRequestUri()); die;
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$this->service->addPage($form->getValidValues($formData));
				$this->redirect('/admin/pages');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit');
	}
	
	public function editAction(){
		$form = new Application_Form_PageForm();
		$this->view->form = $form;
		$this->view->actionType = 'edit';
		
		$id = $this->_getParam('id',false);
		$pageData = $this->service->getPageById($id);
		
		
		$form->url->setValue($pageData['url']);
		$form->name_ru->setValue($pageData['page_name_ru']);
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
				$this->redirect((!empty($page))?'/admin/pages/index/page/'.$page :'/admin/pages');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit');
	}
	
	public function removeAction($id){
		$id = $this->_getParam('id',false);
		if(null !== $id){
			$article = $this->service->getPageById($id);
			$this->service->deletePage($id);
			$page = $this->_getParam('page','');
			$this->redirect((!empty($page))?'/admin/pages/index/page/'.$page :'/admin/pages');
		}
	}
	public function searchAction(){
		$this->_helper->layout->disableLayout();
		$query = $this->_getParam('query','');
		$data = $this->service->searchPage($query);
		$this->view->data = $data;
		$this->render('search');
	}
}