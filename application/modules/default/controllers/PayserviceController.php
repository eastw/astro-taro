<?php
class PayserviceController extends App_Controller_Action_ParentController{
	
	
	protected $service; 
	
	public function init(){
		$this->service = new App_PayserviceService();
	}
	
	public function indexAction(){
		$type = $this->_getParam('type',false);
		$theme = $this->_getParam('theme',false);
		
		if(in_array($type,array('divination','horoscope'))){
			$this->view->serviceType = $type;
			$this->view->topMenuActiveItem = 'service';
			if(!empty($theme)){
				//theme selected
				if(in_array($theme,$this->service->getThemesArray(),true)){
					$themeData = $this->service->getThemeByAlias($theme);
					
					$this->view->themeData = $themeData;
					
					$navItem1 = $this->view->navigation()->findOneById('service-'.$type);
					if($navItem1){
						$navItem2 = $navItem1->findOneById($themeData['id'].'-'.$themeData['theme_smalltype']);
						if($navItem2){
							$navItem2->setActive('true');
						}
					}
					
					
					$this->view->pageTitle = $themeData['theme_name'];
					$this->view->seotitle = (!is_null($themeData['seo-title']))?$themeData['seo-title']:'';
					$this->view->seokeywords = (!is_null($themeData['seo-keywords']))?$themeData['seo-keywords']:'';
					$this->view->seodescription = (!is_null($themeData['seo-description']))?$themeData['seo-description']:'';
					$this->view->minidesc = $themeData['description'];
					//$this->view->socialDescription = $themeData['description'];
					
					$form = null;
					if($type == 'horoscope'){
						if($themeData['double_form'] == 'y'){
							$form = new Application_Form_PayserviceHoroscopeDoubleThemeForm();
						}else{
							$form = new Application_Form_PayserviceHoroscopeThemeForm();
						}
					}else{
						$form = new Application_Form_PayserviceDivinationThemeForm();
					}
					$this->view->payServices = $this->service->getThemesByType($type);
					$this->view->curPayserviceAlias = $theme;
					$this->view->currentServiceAlias = $theme;
					//var_dump($this->view->payServices); die;
					$form->fillPayGates($this->service->listGates());
					$form->alias->setValue($theme);
					$form->summ->setValue($themeData['cost']);
					if(isset($this->view->userdata->fullname) && !empty($this->view->userdata->fullname)){
						if(isset($form->name) ){
							$form->name->setValue(str_replace(':',' ',$this->view->userdata->fullname));
						}else{
							$form->name1->setValue(str_replace(':',' ',$this->view->userdata->fullname));
						}
					}
					if(isset($this->view->userdata->email) && !empty($this->view->userdata->email)){
						$form->email->setValue($this->view->userdata->email);
					}
					if(isset($this->view->userdata->birthday)){
						$year = date('m',strtotime($this->view->userdata->birthday));
						$form->fillDays(date('m',strtotime($this->view->userdata->birthday)),date('m',strtotime($this->view->userdata->birthday)));
						
						if(isset($form->year1)){
							$form->year1->setValue(date('Y',strtotime($this->view->userdata->birthday)));
							$form->month1->setValue(date('m',strtotime($this->view->userdata->birthday)));
							$form->day1->setValue(date('d',strtotime($this->view->userdata->birthday)));
							
							$form->year2->setValue(date('Y'));
							$form->month2->setValue(date('m'));
							$form->day2->setValue(date('d'));
						}else{
							$form->year->setValue(date('Y',strtotime($this->view->userdata->birthday)));
							$form->month->setValue(date('m',strtotime($this->view->userdata->birthday)));
							$form->day->setValue(date('d',strtotime($this->view->userdata->birthday)));
						}
					}else{
						$form->fillDays();
						if(isset($form->year1)){
							$form->year1->setValue(date('Y'));
							$form->month1->setValue(date('m'));
							$form->day1->setValue(date('d'));
							
							$form->year2->setValue(date('Y'));
							$form->month2->setValue(date('m'));
							$form->day2->setValue(date('d'));
						}else{
							$form->year->setValue(date('Y'));
							$form->month->setValue(date('m'));
							$form->day->setValue(date('d'));
						}
					}
					$this->view->attributes = array(
						'type' => 'payservice',
						'subtype' => $theme,
						'sign' => '',
						'resource_id' => '' 
					);
					$this->view->comments = $this->commentsService->getComments('payservice', $theme, '', '');
					$this->view->form = $form; 
					//var_dump($themeData); die;
					$this->render('theme');
				}else{
					throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
				}
			}else{
				//list of services
				$navItem = $this->view->navigation()->findOneById('service-'.$type);
				if($navItem){
					$navItem->setActive('true');
				}
				
				$this->view->typeItems = $this->service->getThemesByType($type);
				if($type == 'horoscope'){
					$this->view->pageTitle = 'Индивидуальный гороскоп';
				}else{
					$this->view->pageTitle = 'Индивидуальное гадание';
				}
			}
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	public function sendOrderAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$form = null;
		$type = $this->_getParam('form_type');
		$partner = $this->_getParam('partner');
		if($type == 'horoscope'){
			if($partner == 'y'){
				$form = new Application_Form_PayserviceHoroscopeDoubleThemeForm();
			}else{
				$form = new Application_Form_PayserviceHoroscopeThemeForm();
			}
		}else{
			$form = new Application_Form_PayserviceDivinationThemeForm();
		}
		
		if($form->isValid($this->_getAllParams())){
			$mailService = new App_MailService();
			$theme = $this->service->getThemeByAlias($this->_getParam('alias',false));
			if($theme){
				$data = $form->getValidValues($this->_getAllParams());
				$email = $this->service->getEmail();
				//var_dump($email); die;
				$mailService->sendPayServiceMail($data,$theme,$email);
			}
			echo Zend_Json::encode(array());
		}else{
			echo Zend_Json::encode($form->getMessages());
			//$messages = $form->getMessages();
			//var_dump($messages); die;
		}
		//var_dump($this->_getAllParams()); die;
	}
}