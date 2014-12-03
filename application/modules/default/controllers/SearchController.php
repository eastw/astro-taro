<?php
class SearchController extends App_Controller_Action_ParentController{
	
	protected $service;
	
	public function init(){
		$this->service = new App_SearchService();
	}
	
	public function indexAction(){
		$query = $this->_getParam('query',false);
		$page = $this->_getParam('page',false);
		$query = preg_replace('/<\/?[^>]+>/ims','',$query);
		$page = preg_replace('/<\/?[^>]+>/ims','',$page);
		$this->view->pageTitle = 'Вы искали: '.$query;
		$this->view->searchQuery = $query;
		
		$navItem = $this->view->navigation()->findOneById('search');
		if($navItem){
			$navItem->setActive('true');
		}
		
		$this->preparePage($page);
		$this->view->data = $this->service->search($query,$page);
		$this->view->pagination = $this->service->getPagesArray();
	}
	
	protected function preparePage($page){
		if(is_numeric($page)){	
			if($page < 0){
				$this->view->curPage = 1;
			}else{
				$this->view->curPage = $page;
			}
		}else{
			$this->view->curPage = 1;
		}
		//$this->view->curPage = $page;
	}
}