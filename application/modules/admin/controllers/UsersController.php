<?php
class Admin_UsersController extends Zend_Controller_Action{
	
	protected $service;
	
	public function preDispatch(){
		parent::preDispatch();
		$this->service = new App_UserService();
	}
	
	public function indexAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->service->listUsersQuery();
		$this->view->page = $page;
		
		$this->view->usersCount = $this->service->usersCount();
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function changeActivityAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id');
		if($id){
			echo Zend_Json::encode($this->service->changeUserActivity($id));
		}
	}
	public function searchAction(){
		$this->_helper->layout->disableLayout();
		$query = $this->_getParam('query','');
		$data = $this->service->searchUser($query);
		$this->view->data = $data;
		//$this->render('search');
	} 
	
	
}