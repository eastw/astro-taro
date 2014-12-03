<?php
class Admin_TagController extends Zend_Controller_Action{
	
	protected $service;
	protected $navigation;
	
	public function preDispatch(){
		$this->service = new App_TagService();
		$this->navigation = new App_NavigationService();
	}
	
	public function indexAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->service->buildArticleTagsQuery();
		
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function addAction(){
		$form = new Application_Form_TagForm();
		$this->view->form = $form;
		$this->view->actionType = 'add'; 
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$cleanValues = $form->getValidValues($formData);
				$this->service->addArticleTag($cleanValues);
				$this->navigation->refreshNavigation();
				$this->redirect('/admin/tag');
			}else{
				$form->populate($formData);
			}
		}
		$this->render('edit');
	}
	
	public function editAction(){
		$form = new Application_Form_TagForm();
		$this->view->form = $form;
		$this->view->actionType = 'edit';
		
		$id = $this->_getParam('id');
		$tag = $this->service->getTagById($id);
		
		
		$page = $this->_getParam('page','');
		$form->getElement('page')->setValue($page);
		
		$form->getElement('tagname')->setValue($tag['tagname']);
		$form->getElement('description')->setValue($tag['description']);
		$form->getElement('seokeywords')->setValue($tag['seo-keywords']);
		$form->getElement('seodescription')->setValue($tag['seo-description']);
		
		
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$cleanValues = $form->getValidValues($formData);
				$this->service->saveArticleTag($cleanValues,$tag['id']);
				$this->navigation->refreshNavigation();
				$this->redirect((!empty($page))?'/admin/tag/index/page/'.$page :'/admin/tag');
			}else{
				$form->populate($formData);
			}
		}
	}
	
	public function searchAction(){
		$this->_helper->layout->disableLayout();
		$query = $this->_getParam('query','');
		if(!empty($query)){
			$data = $this->service->searchArticleTag($query);
			$this->view->data = $data;
			$this->render('search');
		}
	}
	
	public function removeAction(){
		$this->service->removeTag($this->_getParam('id'));
		$this->navigation->refreshNavigation();
		$page = $this->_getParam('page','');
		$this->redirect((!empty($page))?'/admin/tag/index/page/'.$page :'/admin/tag');
	}
	
	
	public function clearSearchAction(){
		$page = 1;
		
		$query = $this->service->buildArticleTagsQery();
		
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(10);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
}