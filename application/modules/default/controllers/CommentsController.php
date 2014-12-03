<?php
class CommentsController extends App_Controller_Action_ParentController{
	
	protected $service;
	
	public function init(){
		$this->service = new App_CommentsService();
	}
	
	public function addAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$data = $this->_getAllParams();
		echo Zend_Json::encode($this->service->addComment($data,$this->view->userdata));
	}
	public function abuseAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		$type = $this->_getParam('type',false);
		$abuse = $this->_getParam('abuse',false);
		if($id && $type ){
			$this->service->abuseComment($id,$type,$abuse);
		}
	}
	
	public function addPayserviceCommentAction(){
		$this->_helper->layout->disableLayout();
		$data = $this->_getAllParams();
		$this->service->addComment($data,$this->view->userdata);
		$this->view->comments = $this->service->getPayserviceComments();
		$this->render('payservice-comments');
	}
}