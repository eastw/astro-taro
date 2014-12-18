<?php
class HoroscopeController extends App_Controller_Action_ParentController{
	
	protected $service;
	protected $types;
	protected $signs;
	
	public function init(){
		$this->service = new App_HoroscopeService();
		$this->types = $this->service->getFrontendHoroscopeTypes();
		$this->signs = $this->service->getSimpleSunSigns();
	}
	
	public function indexAction(){
		$type = $this->_getParam('type',false);
		$sign = $this->_getParam('sign',false);
		if($type && $sign){
			if(array_key_exists($type, $this->types) && in_array($sign, $this->signs)){
				if($type != 'list'){
					$this->view->curSign = $sign;
					foreach($this->view->signs as $item){
						if($item['sign'] == $sign){
							$this->view->pageTitle = $item['sign_ru'].' — гороскопы';
							$this->prepareMetaData($type,$item['sign_ru']);
							if($type == 'simple'){
								$this->view->pageTitle = $item['sign_ru'].' — характеристика знака';
							}
							if($type == 'love-compability'){
								$this->view->pageTitle = $item['sign_ru'].' — любовная совместимость';
							}
							if($type == 'business-compability'){
								$this->view->pageTitle = $item['sign_ru'].' — гороскоп совместимости';
							}
							if($type == 'profession'){
								$this->view->pageTitle = $item['sign_ru'].' — гороскоп профессии';
							}
							if($type == 'karma'){
								$this->view->pageTitle = 'Кармический гороскоп';
							}
							if($type == 'health'){
								$this->view->pageTitle = $item['sign_ru'].' — гороскоп здоровья';
							}
							if($type == 'child'){
								$this->view->pageTitle = $item['sign_ru'].' — гороскоп ребенка';
							}
							if($type == 'business'){
								$this->view->pageTitle = $item['sign_ru'].' — бизнес гороскоп';
							}
							if($type == 'week'){
								$this->view->pageTitle = $item['sign_ru'].' — гороскоп на неделю';
							}
							if($type == 'month'){
								$this->view->pageTitle = $item['sign_ru'].' — гороскоп на месяц';
							}
							if($type == 'year'){
								$this->view->pageTitle = 'Гороскоп на ' . date('Y') . ' год ' . $item['sign_ru'];
							}
							if($type == 'next-year'){
								$this->view->pageTitle = 'Гороскоп на ' . date('Y', strtotime('+1 year')) . ' год ' . $item['sign_ru'];
							}
						}
					}
					$this->view->attributes = array(
						'type' => 'horoscope',
						'subtype' => $type,
						'sign' => $sign,
						'resource_id' => '' 
					);
					$this->view->comments = $this->commentsService->getComments('horoscope', $type, $sign, '');
					
					$navItem1 = $this->view->navigation()->findOneById('horoscope-'.$sign.'-today');
					if($type != 'today'){
						if($navItem1){
							$navItem2 = $navItem1->findOneById('horoscope-'.$sign.'-'.$type);
							if($navItem2){
								$navItem2->setActive('true');
								if($type == 'next-year'){
									$navItem2->setLabel($this->view->pageTitle);
								}
							}
						}
					}else{
						if($navItem1){
							$navItem1->setActive('true');
						}
					}
				}else{
					$this->view->sliderExist = true;
					$this->view->curSign = '';
					$this->view->pageTitle = 'Гороскопы';
					
					$navItem = $this->view->navigation()->findOneById('horoscope-list');
					if($navItem){
						$navItem->setActive('true');
					}
				}
				
				$this->view->type = $type;
				$this->view->topMenuActiveItem = 'horoscope';
				
				$this->prepareData($type,$sign);
				$this->render($type);
			}else{
				throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
			}
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	protected function prepareMetaData($type,$sign){
		$pages = $this->service->getAllPages();
		foreach($pages as $page){
			if($page['horoscope_type'] == $type){
				$this->view->seotitle = App_UtilsService::replaceVariables($page['title'],$sign);//$title;
				$this->view->seokeywords = App_UtilsService::replaceVariables($page['keywords'],$sign);//str_replace('@sign',$sign,$page['keywords']);
				$this->view->seodescription = App_UtilsService::replaceVariables($page['description'],$sign);//str_replace('@sign',$sign,$page['description']);
				$this->view->minidesc = App_UtilsService::replaceVariables($page['minidesc'],$sign);//str_replace('@sign',$sign,$page['minidesc']);
				$this->view->socialDescription = $this->view->minidesc;//str_replace('@sign',$sign,$this->view->minidesc);
			}
		}
	}
	
	protected function prepareData($type,$sign){
		$this->view->signs = $this->service->getSunSigns();
		$this->prepareListData();
		switch($type){
			case 'today': $this->prepareTodayData($sign); break;
			case 'week': $this->prepareWeekData($sign); break;
			case 'month': $this->prepareMonthData($sign); break;
			case 'year': $this->prepareYearData($sign); break;
			case 'next-year': $this->prepareNextYearData($sign); break;
			case 'business': $this->prepareBusinessData($sign); break;
			case 'child': $this->prepareChildData($sign); break;
			case 'health': $this->prepareHealthData($sign); break;
			case 'profession': $this->prepareProfessionData($sign); break;
			case 'karma': $this->prepareKarmaData($sign); break;
			case 'love-compability': $this->prepareCompabilityData($sign); break;
			case 'business-compability': $this->prepareCompabilityData($sign); break;
			case 'simple': $this->prepareSimpleData($sign); break;
		}
	}
	
	protected function prepareSimpleData($sign){
		$this->view->sign = $this->service->getSignByAlias(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_SUN, $sign);
	}
	
	protected function prepareCompabilityData($sign){
		$this->view->signs = $this->service->getSunSigns();
		if((isset($this->view->userdata) && !empty($this->view->userdata)
			&& (isset($this->view->userdata->birthday) && !empty($this->view->userdata->birthday))
				&& (null !== $this->view->userdata->sun_sign_id)	
		))
		{
			$userSign = $this->view->userdata->sun_sign_alias;
			foreach($this->view->signs as $item){
				if($item['sign'] == $userSign){
					$this->view->userSign = $item;
				}
				if($item['sign'] == $sign){
					$this->view->sign = $item;
				}
			}
		}else{
			foreach($this->view->signs as $item){
				if($item['sign'] == $sign){
					$this->view->sign = $item;
					$this->view->userSign = $item;
				}
			}
		}
	}
	
	public function getCompabilityAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$type = $this->_getParam('type',false);
		if($type){
			$type_id = 0;
			$maingender = '';
			$nestedgender = '';
			if($type == 'love'){
				$type_id = App_HoroscopeService::HOROSCOPE_COMPABILITY_TYPE_LOVE;
				if(isset($this->view->userdata) && !empty($this->view->userdata) && $this->view->userdata->gender == 'm'){
					$maingender = 'man';
					$nestedgender = 'woman';
				}else{
					$maingender = 'woman';
					$nestedgender = 'man';
				}
			}elseif($type == 'business'){
				$type_id = App_HoroscopeService::HOROSCOPE_COMPABILITY_TYPE_BUSINESS;
				$maingender = 'man';
				$nestedgender = 'man';
			}
			$sign1 = $this->_getParam('sign1',false);
			$sign2 = $this->_getParam('sign2',false);
			echo Zend_Json::encode($this->service->getCompabilityItem($type_id,$sign1, $sign2, $maingender, $nestedgender));
		}
	}
	
	protected function prepareHealthData($sign){
		$this->view->data = $this->service->getHoroscopeByTypeAndSignAlias(App_HoroscopeService::HOROSCOPE_TYPE_HEALTH, $sign);
	}
	protected function prepareBusinessData($sign){
		$this->view->data = $this->service->getHoroscopeByTypeAndSignAlias(App_HoroscopeService::HOROSCOPE_TYPE_BUSINESS, $sign);
	}
	protected function prepareChildData($sign){
		$this->view->data = $this->service->getHoroscopeByTypeAndSignAlias(App_HoroscopeService::HOROSCOPE_TYPE_CHILD, $sign);
	}
	protected function prepareProfessionData($sign){
		$this->view->data = $this->service->getHoroscopeByTypeAndSignAlias(App_HoroscopeService::HOROSCOPE_TYPE_PROF, $sign);
	}
	
	protected function prepareKarmaData($sign){
		$this->view->form = new Application_Form_KarmaForm();
		
		if(isset($this->view->userdata->birthday) && !empty($this->view->userdata->birthday)){
			$this->view->form->byear->setValue(date('Y',strtotime($this->view->userdata->birthday))); 
			$this->view->form->bmonth->setValue(date('m',strtotime($this->view->userdata->birthday)));
			$num = cal_days_in_month(CAL_GREGORIAN, date('m',strtotime($this->view->userdata->birthday)), date('Y',strtotime($this->view->userdata->birthday))); // 31
			$days = array();
			for($i = 1; $i < ($num + 1); $i++){
				$days[$i] = $i;
			}
			$this->view->form->bday->addMultiOptions($days);
			$this->view->form->bday->setValue(date('d',strtotime($this->view->userdata->birthday)));
		}
	}

	public function getKarmaDescriptionAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$json = array();
		if($this->getRequest()->isPost()){
			$form = new Application_Form_KarmaForm();
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$validData = $form->getValidValues($formData);
				$birthday = $validData['byear'].'-'.$validData['bmonth'].'-'.$validData['bday'];
				$json = $this->service->getKarmaByBirthday($birthday);
			}
		}
		echo Zend_Json::encode($json);
	}
	
	protected function prepareListData(){
		$horoscopeTypes = $this->service->getFrontendHoroscopeTypes();
		unset($horoscopeTypes['list']);
		$count = 0;
		$types = array('vis' =>array(),'unvis' => array());
		foreach($horoscopeTypes as $type=>$text){
			if($count <= 4){
				$types['vis'][$type] = $text;
			}else{
				$types['unvis'][$type] = $text;
			}
			$count ++;
		}
		$this->view->horoscopeListTypes = $types;
		$this->view->horoscopeTypes = $horoscopeTypes;
		 
	}
	protected function prepareTodayData($sign){
		$today = date('Y-m-d');
		$tomorrow = date('Y-m-d',strtotime('+1 day'));
		$date = new Zend_Date(date('Y-m-d'));
		$this->view->today = $date->toString(Zend_Date::DATE_LONG);
		$this->view->tomorrow = (new Zend_Date($tomorrow))->toString(Zend_Date::DATE_LONG);
		$this->view->data = $this->service->getTodayDataBySignAlias($today,$tomorrow,$sign);
	}
	protected function prepareWeekData($sign){
		$startdate = date('Y-m-d',strtotime('last monday'));
		$enddate = date('Y-m-d',strtotime($startdate.'+1 week -1 day'));
		$date = new Zend_Date($startdate);
		$this->view->startdate = $date->toString(Zend_Date::DATE_LONG);
		$date = new Zend_Date($enddate);
		$this->view->enddate = $date->toString(Zend_Date::DATE_LONG);
		$this->view->data = $this->service->getWeekDataBySignAlias($startdate,$enddate,$sign);
	}
	protected function prepareMonthData($sign){
		$startdate = date('Y-m').'-01';
		$enddate = date('Y-m-d',strtotime($startdate.' +1 month -1 day'));
		$date = new Zend_Date($startdate);
		$this->view->startdate = $date->toString(Zend_Date::DATE_LONG);
		$date = new Zend_Date($enddate);
		$this->view->enddate = $date->toString(Zend_Date::DATE_LONG);
		
		$this->view->data = $this->service->getMonthDataBySignAlias($startdate,$enddate,$sign);
	}
	
	protected function prepareYearData($sign){
		$startdate = date('Y').'-01-01';
		$enddate = date('Y-m-d',strtotime($startdate.' +1 year -1 day'));
		$this->view->data = $this->service->getYearDataBySignAlias($startdate,$enddate,$sign);
	}

	protected function prepareNextYearData($sign){
		$startdate = date('Y',strtotime('+1 year')).'-01-01';
		$enddate = date('Y-m-d',strtotime($startdate.' +1 year -1 day'));
		$this->view->data = $this->service->getYearDataBySignAlias($startdate,$enddate,$sign);
	}

	public function postDispatch(){
		
	}
	
}