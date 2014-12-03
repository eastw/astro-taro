<?php
class App_Controller_Plugin_AclPlugin extends Zend_Controller_Plugin_Abstract{
	protected $_auth;
	
	protected $_acl;
	protected $_module;
	protected $_action;
	protected $_controller;
	protected $_currentRole;
	
	public function __construct(Zend_Acl $acl, array $options = array()) {
		$this->_auth = Zend_Auth::getInstance();
		$this->_auth->setStorage(new Zend_Auth_Storage_Session('astroauth'));
		//var_dump($this->_auth); die;
		$this->_acl = $acl;
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$this->_init($request);
		//TODO: unrem try catch block
		try{
			if (!$this->_acl->isAllowed($this->_currentRole, $this->_module.'-'.$this->_controller, $this->_action)) {
				$request->setModuleName('default');
				$request->setControllerName('user');
				$request->setActionName('noauth');
			}
			
		}catch(Exception $ex){
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
		
		//echo '!!!'; die;
	}
	
	protected function _init($request) {
		$this->_module = $request->getModuleName();
		$this->_action = $request->getActionName();
		$this->_controller = $request->getControllerName();
		$this->_currentRole = $this->_getCurrentUserRole();
	}
	
	protected function _getCurrentUserRole() {
		if ($this->_auth->hasIdentity()) {
			$authData = $this->_auth->getIdentity();
			$role = isset($authData->role)?strtolower($authData->role): 'guest';
		} else {
			$role = 'guest';
		}
	
		return $role;
	}
	
}