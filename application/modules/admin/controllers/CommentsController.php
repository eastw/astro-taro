<?php
class Admin_CommentsController extends Zend_Controller_Action{
	
	protected $service;
	protected $userService;
	protected $payserviceService;
	
	public function preDispatch(){
		$this->service = App_CommentsService::getInstance();
		$this->userService = new App_UserService();
		$this->payserviceService = new App_PayserviceService();
	}
	
	public function indexAction(){
		$page = $this->_getParam('page','');
		$type = $this->_getParam('type',false);
		$subtype = $this->_getParam('subtype',false);
		$sign = $this->_getParam('sign',false);
		$resource = $this->_getParam('resource',false);
		$user = $this->_getParam('user',false);
		
		if($type){
			$this->view->type = $type;
			$this->view->subtypes = $this->service->getSubTypes($type);
			if($type == 'payservice'){
				$this->view->themes = $themes = $this->payserviceService->listThemes();
			}
			//if($type == 'horoscope'){
			$this->view->signs = $this->service->getSigns(); 
			//}
		}else{
			$this->view->type = 'all';
		}
		if($subtype){
			$this->view->subtype = $subtype;
		}
		if($sign){
			$this->view->sign = $sign;
		}
		if($resource){
			$this->view->resource = $resource;
			$this->view->resourceDetails = $this->service->getResourceByIdAndType($resource,$type); 
		}
		if($user){
			$this->view->user = $user;
			$this->view->userDetails = $this->userService->getUserById($user)->toArray();
		}
		
		//var_dump($this->_getAllParams()); die;
		
		$query = $this->service->buildCommentsQuery($type,$subtype,$sign,$resource,$user);
		
		$this->view->page = $page;
		$this->view->types = $this->service->getTypes();
		
		//var_dump($this->getRequest()->getParams()); die;
		$this->view->requestParams = $this->getRequest()->getParams();
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function dataByTypeAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$type = $this->_getParam('type',false);
		$result = array();
		$result['subtypes'] = $this->service->getSubTypes($type);
		echo Zend_Json::encode($result);
		
	}
	public function getUserAutocompleteAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$mask = $this->_getParam('query',false);
		if($mask){
			echo Zend_Json::encode($this->service->getUsersByMask($mask));
		}
	}
	public function getResourceAutocompleteAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$mask = $this->_getParam('query',false);
		$type = $this->_getParam('type',false);
		if($mask && $type){
			echo Zend_Json::encode($this->service->getResourcesByMask($mask,$type));
		}
	}
	public function removeAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id');
		if($id){
			$this->service->removeComment($id);
		}
	}
	public function removeAllAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$userId = $this->_getParam('id');
		if($userId){
			$this->service->removeAllUserComments($userId);
		}
	}
	
	/*
	public function searchAction(){
		$this->_helper->layout->disableLayout();
		$query = $this->_getParam('query','');
		$data = $this->service->searchArticle($query);
		$this->view->data = $data;
		$this->render('search');
	}
	
	public function addtagAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$tag_id = $this->_getParam('tag_id');
		$tagname = $this->_getParam('tagname');
		
		$session = new Zend_Session_Namespace('addtag');
		if(!isset($session->tags)){
			$session->tags = array();
		}
		if(!array_key_exists($tag_id,$session->tags)){
			$session->tags[$tag_id] = $tagname;
		}
		
	}
	
	public function removetagAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$tag_id = $this->_getParam('tag_id');
		$session = new Zend_Session_Namespace('addtag');
		if(!isset($session->tags)){
			$session->tags = array();
		}
		$tags = $session->tags;
		foreach($tags as $index => $tag){
			if($tag_id == $index){
				unset($tags[$index]);
			}
		}
		//var_dump($tags); 
		$session->tags = $tags;
	}
	*/
}