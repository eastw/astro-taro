<?php
class ProfileController extends App_Controller_Action_ParentController{
	
	protected $service;
	
	protected $userService;
	
	public function preDispatch(){
		parent::preDispatch();
		$this->service = new App_ProfileService($this->view->userdata);
		$this->userService = new App_UserService();
	}
	
	public function indexAction(){
		
		$this->view->pageTitle = 'Мой Astro Tarot';
		$navItem = $this->view->navigation()->findOneById('profile');
		if($navItem){
			$navItem->setActive('true');
		}
		//var_dump($this->view->userdata); die;
		//$this->view->profile = $this->service->calcUserProfile($this->view->userdata);
		//var_dump($this->view->userdata); die;
		$this->view->profile = $this->service->calcProfileParts();
		//echo '<pre>';
		//var_dump($this->view->profile); die;
	}
	
	public function getProfileDescriptionAction(){
		$type = $this->_getParam('type',false);
		$types = array('sun','kelt','china','karma','number','taro');
		if($type && in_array($type,$types)){
			$navItem = $this->view->navigation()->findOneById($type.'-description');
			if($navItem){
				$navItem->setActive('true');
			}
			$this->view->pageTitle = App_UtilsService::profileTypeToRu($type);
			$this->view->sign = $this->service->getSignDescription($type);
			//var_dump($this->view->sign); die;
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	public function editAction(){
		$this->view->pageTitle = 'Мой профиль';
		//$this->view->navigation()->findOneById('profile-edit')->setActive('true');
		
		$avatar = new Application_Form_AvatarForm();
		if(empty($this->view->userdata->avatar)){
			$avatar->img_note->setValue('<img id="avatar-img" src="/files/avatar/cabinet_profile.png"/>'); ;
		}else{
			$avatar->img_note->setValue('<img id="avatar-img" src="/files/avatar/'.$this->view->userdata->avatar.'"/>');
		}
		$this->view->avatar = $avatar;
		
		$navItem = $this->view->navigation()->findOneById('profile-edit');
		if($navItem){
			$navItem->setActive('true');
		}
		
		$form = new Application_Form_ProfileForm();
		if(!empty($this->view->userdata->birthday)){
			$date = new Zend_Date($this->view->userdata->birthday);
			$form->byear->setValue($date->get(Zend_Date::YEAR));
			$form->bmonth->setValue($date->get(Zend_Date::MONTH));
			$num = cal_days_in_month(CAL_GREGORIAN, $date->get(Zend_Date::MONTH), $date->get(Zend_Date::YEAR)); // 31
			$days = array();
			for($i = 1; $i < ($num + 1); $i++){
				$days[$i] = $i;
			}
			$form->bday->addMultiOptions($days);
			$form->bday->setValue($date->get(Zend_Date::DAY));
		}
		if(isset($this->view->userdata->gender) && !empty($this->view->userdata->gender)){
			$form->gender->setValue($this->view->userdata->gender);
		}
		//var_dump($this->view->userdata); die;
		if(isset($this->view->userdata->fullname) && !empty($this->view->userdata->fullname)){
			//
			$names = explode(':',$this->view->userdata->fullname);
			//var_dump($names); die;
			$form->fname->setValue($names[0]);
			$form->mname->setValue($names[1]);
			$form->lname->setValue($names[2]);
		}else{
			$form->fname->setValue('не заполнено');
			$form->mname->setValue('не заполнено');
			$form->lname->setValue('не заполнено');
		}
		$form->email->setValue($this->view->userdata->email);
		$this->view->form = $form;
	}
	
	public function checkProfileAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$form = new Application_Form_ProfileForm();
		$json = array();
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			$form = $this->prepareForm($form, $formData);
			if($form->isValid($formData)){
				$json['result'] = 'success';
				$json['errors'] = array();
				$validData = $form->getValidValues($formData);
				$this->userService->saveUser($validData,$this->view->userdata->id);
				$this->service->calcProfileParts();
				//var_dump($this->view->userdata); die;
				if(isset($this->view->userdata->sun_sign_alias) && !empty($this->view->userdata->sun_sign_alias)){
					$json['sun_sign'] = $this->view->userdata->sun_sign_alias;
				}else{
					$json['sun_sign'] = '';
				}
				if(isset($this->view->userdata->lifenumber) && !empty($this->view->userdata->lifenumber)){
					$json['lifenumber'] = 'not empty'; 
				}else{
					$json['lifenumber'] = '';
				}
			}else{
				$json['result'] = 'fail';
				$json['errors'] = $form->getMessages();
			}
		}
		echo Zend_Json::encode($json);
	}
	/*
	$storage = Zend_Auth::getInstance()->getStorage()->read();
	$storage->row_num = $row_num;
	Zend_Auth::getInstance()->getStorage()->write($storage);
	*/
	protected function prepareForm($form,&$formData){
		if(!empty($formData['pass']) && !empty($formData['pass_confirm'])){
			$form->pass->setRequired(true);
			$form->pass_confirm->setRequired(true);
			$form->pass->addValidator(new Zend_Validate_StringLength(
						array(
								'min' => 6,
								'max' => 12)));
			$form->pass_confirm->addValidator(new App_Validate_PasswordConfirmation());
		}
		if($this->view->userdata->email != $formData['email']){
			$form->email->setRequired(true);
			$form->email->addValidator(new App_Validate_ExistEmail());
		}
		if( (!empty($formData['fname']) && $formData['fname'] != 'не заполнено')  
			|| (!empty($formData['mname']) && $formData['mname'] != 'не заполнено' ) 
			|| (!empty($formData['lname']) && $formData['lname'] != 'не заполнено') ){
			
			$form->getElement('fname')->setRequired(true);
			$form->getElement('mname')->setRequired(true);
			$form->getElement('lname')->setRequired(true);
			
			if($formData['fname'] == 'не заполнено'){
				$formData['fname'] = '';
			}
			if($formData['mname'] == 'не заполнено'){
				$formData['mname'] = '';
			}
			if($formData['lname'] == 'не заполнено'){
				$formData['lname'] = '';
			}
		}
		if( !empty($formData['byear']) 
		|| !empty($formData['bmonth']) 
		|| !empty($formData['bday']) ){
				
			$form->getElement('byear')->setRequired(true);
			$form->getElement('bmonth')->setRequired(true);
			$form->getElement('bday')->setRequired(true);
		}
		
		return $form;
	}
	
	public function removeAction(){
		$this->userService->deleteUser($this->view->userdata->id);
		$this->userService->signOut();
		$this->redirect('/');
	}
	
	public function changeAvatarAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$form = new Application_Form_AvatarForm();
		$formData = $this->_getAllParams();
		$json = array();
		$validData = array();
		if($form->isValid($formData)){
			$adapter = $form->image->getTransferAdapter();
			foreach ($adapter->getFileInfo() as $file) {
				$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
				$newName = uniqid().'.'.$ext;
				$path = realpath(dirname('.')).
				DIRECTORY_SEPARATOR.
				'files'.
				DIRECTORY_SEPARATOR.
				'avatar'.
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
			if(!empty($this->view->userdata->avatar ) && file_exists(APPLICATION_PATH. '/../public/files/avatar/'.$this->view->userdata->avatar)){
				unlink(APPLICATION_PATH. '/../public/files/avatar/'.$this->view->userdata->avatar);
			}
			$this->userService->setAvatar($validData,$this->view->userdata->id);
			$json['result'] = 'success';
			$json['newavatar'] = $validData['image']; 
		}else{
			//var_dump($form->getMessages());
			$json['result'] = 'fail';
			$json['errors'] = $form->getMessages();
		}
		echo Zend_Json::encode($json); 
	}
	
	public function removeAvatarAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		if(!empty($this->view->userdata->avatar ) && file_exists(APPLICATION_PATH. '/../public/files/avatar/'.$this->view->userdata->avatar)){
			unlink(APPLICATION_PATH. '/../public/files/avatar/'.$this->view->userdata->avatar);
		}
		$this->userService->setAvatar(array('image' => ''),$this->view->userdata->id);
	}
	
	public function favoriteAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		$type = $this->_getParam('type',false);
		$subtype = $this->_getParam('subtype',false);
		if($id && $type){
			echo Zend_Json::encode($this->service->addFavorite($id, $type,$this->view->userdata->id,$subtype));
		}
	}
	
	public function favoriteListAction(){
		$this->view->pageTitle = 'Мое избранное';
		$favorites = $this->service->listFavorites($this->view->userdata->id);
		//var_dump($favorites); die;
		$this->view->favorites = $favorites;
		$navItem = $this->view->navigation()->findOneById('profile-favorite');
		if($navItem){
			$navItem->setActive('true');
		}
		//var_dump($favorites); die;
	}
	
	public function favoriteRemoveAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		if($id && is_numeric($id)){
			$this->service->deleteFavorite($id);
		}
	}
	
	public function dayDescriptionAction(){
		$type = $this->_getParam('type');
		$types = array('taro','rune','hexagramm','number');
		if(in_array($type, $types)){
			$navItem = $this->view->navigation()->findOneById('profile-'.$type.'-today');
			if($navItem){
				$navItem->setActive('true');
			}
			$indexService = new App_IndexService();
			switch ($type){
				case 'taro' : 
						$this->view->data = $this->service->getDayData($type,$indexService->taroDay($this->view->taroDay,$this->view->taroDayState));
						$this->view->pageTitle = 'Карта Таро дня';
						break;
				case 'rune' : 
						$this->view->data = $this->service->getDayData($type,$indexService->runeDay($this->view->runeDay,$this->view->runeDayState));
						$this->view->pageTitle = 'Руна дня';
						break;
				case 'hexagramm' : 
						$this->view->data = $this->service->getDayData($type,$indexService->hexagrammDay($this->view->hexagrammDay));
						//var_dump($this->view->data); die;
						$this->view->pageTitle = 'Гексаграмма дня';
						break;
				case 'number': $this->view->pageTitle = 'Число дня'; break; 
							
			}
			//var_dump($this->view->runeDayState);
			//var_dump($this->view->data); 
			//die;
			if(isset($this->view->userdata->birthday) && !empty($this->view->userdata->birthday) && $type == 'number'){
				$this->view->data = $this->service->getDayData($type,$indexService->numberDayData($this->view->userdata->birthday));
			}
			$this->view->dayType = $type;
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	public function needPersonalDataAction(){
		$this->view->pageTitle = 'Необходимы персональные данные';
		$navItem = $this->view->navigation()->findOneById('need-personal-data');
		if($navItem){
			$navItem->setActive('true');
		}
	}
	
	public function postDispatch(){
		
	}
}