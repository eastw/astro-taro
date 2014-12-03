<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\FacebookRequestException;


class UserController extends App_Controller_Action_ParentController{
	
	protected $service;
	
	protected $mailservice;
	
	//protected $appId = '4361464';
	//VK auth data
	protected $vkAppId = '4359943';
	protected $fbAppId = '1502387886647001';
	
	//protected $appKey = 'lobojX16JaEFvkCYb9gq';
	//FB auth data
	protected $vkAppKey = 'UahnmjNG6coQEd9hHIMx';
	protected $fbAppKey = 'a6f3cfb79a4300d43eaae6469985e46b';
	
	/*
	//twitter auth data
	protected $consumerKey = '8gNWfu7Dv3Xcp2AaVijFeyBek';
	protected $consumerSecret = 'uRBLJpvj00fKlLGWu00b2uXGYXLotynIHQ1HE9Bg3TEjdl3yBw';
	protected $urlCallback = 'http://astrotarot.ru/user/tweet-auth';
	
	protected $urlRequestToken = 'https://api.twitter.com/oauth/request_token';
	protected $urlAuth = 'https://api.twitter.com/oauth/authorize';
	protected $urlAceessToken = 'https://api.twitter.com/oauth/access_token';
	protected $urlAccountData = 'https://api.twitter.com/1.1/users/show.json';
	
	protected $oauthToken = '';
	protected $oauthTokenSecret = '';
	*/
	
	public function init(){
		$this->service = new App_UserService();
		$this->mailservice = new App_MailService();
	}
	
	public function loginAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		if($this->getRequest()->isPost()){
			$form = new Application_Form_AuthForm();
			$formData= $this->_getAllParams();
			$json = array();
			if($form->isValid($formData)){
				$result = $this->service->auth($form->getValidValues($formData));
				if($result->getCode() == Zend_Auth_Result :: SUCCESS){
					$identity = Zend_Auth::getInstance()->getIdentity();
					if($identity->activity == 'n'){
						$json['result'] = 'fail';
						$json['errors'] = array('login_error' => array('error'=>'Учетная запись заблокирована'));
						$this->service->signOut();
					}else{
						$json['result'] = 'success';
					}
				}else{
					$json['result'] = 'fail';
					$json['errors'] = array('login_error' => array('error'=>'Неверный логин и/или пароль'));
				}
			}else{
				//var_dump($form->getMessages()); die;
				$json['result'] = 'fail';
				$json['errors'] = $form->getMessages();
				//var_dump($json); die;
			}
			echo Zend_Json::encode($json);
		}
	}

	public function registrationAction(){
		if(!isset($this->view->userdata)){
			$simpleForm = new Application_Form_SimpleRegistrationForm();
			$fullForm = new Application_Form_FullRegistrationForm();
			
			$this->view->pageTitle = 'Регистрация';
			
			$navItem = $this->view->navigation()->findOneById('registration');
			if($navItem){
				$navItem->setActive('true');
			}
			
			$this->view->simpleform = $simpleForm;
			$this->view->fullform = $fullForm;
			
		}else{
			$this->redirect('/profile');
		}
	}
	
	
	
	
	public function recoverAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		//$this->view->navigation()->findOneById('recover')->setActive('true');
		if($this->getRequest()->isPost()){
			$form = new Application_Form_RecoverEmailForm();
			if($form->isValid($this->_getAllParams())){
				//echo 'valid'; die;
				$this->service->recoverPassword($form->getValidValues($this->_getAllParams()));
				
			}
			echo Zend_Json::encode($form->getMessages('remail'));
		}
	}
	
	public function refreshCaptchaAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if($this->getRequest()->isPost()){
			$type= $this->_getParam('type',false);
			$json = array();
			if($type && $type == 'simple'){
				$form = new Application_Form_SimpleRegistrationForm();
				$json['new_id'] = $form->captcha->getCaptcha()->generate();
			}elseif($type && $type == 'full'){
				$form = new Application_Form_FullRegistrationForm();
				$json['new_id'] = $form->full_captcha->getCaptcha()->generate();
			}
			echo Zend_Json_Encoder::encode($json);
		}
	}
	
	public function checkFormAction(){
		if(!isset($this->view->userdata)){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			if($this->getRequest()->isPost()){
				$formData = $this->_getAllParams();
				//var_dump($formData); die;
				$form = null;
				$new_id = null;
				$json = array();
				if($formData['formtype'] == 'full'){
					$form = new Application_Form_FullRegistrationForm();
				}elseif($formData['formtype'] == 'simple'){
					$form = new Application_Form_SimpleRegistrationForm();
				}
				if(isset($formData['fname']) && $formData['fname'] == 'Имя'){
					$formData['fname'] = '';
				}
				if(isset($formData['mname']) && $formData['mname'] == 'Отчество'){
					$formData['mname'] = '';
				}
				if(isset($formData['lname']) && $formData['lname'] == 'Фамилия'){
					$formData['lname'] = '';
				}
				if($form){
					$form->isValid($formData);
					$json['errors'] = $form->getMessages();
					if(count($json['errors'])){
						if($formData['formtype'] == 'full'){
							$json['new_id'] = $form->full_captcha->getCaptcha()->generate();
						}else{
							$json['new_id'] = $form->captcha->getCaptcha()->generate();
						}
					}else{
						$validData = $form->getValidValues($formData);
						$this->service->addUser($validData);
						//$this->mailservice->sendRegistrationMail($validData);
						$this->service->auth($validData);
					}
					echo Zend_Json::encode($json);
				}
			}
		}
	}
	
	public function daysAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if($this->getRequest()->isPost()){
			$year = $this->_getParam('year');
			$month = $this->_getParam('month');
			$num = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 31
			$days = array();
			for($i = 1; $i < ($num + 1); $i++){
				$days[$i] = $i;
			}
			echo Zend_Json::encode($days);
			//echo "There was $num days in August 2003";
		}
	}
	/*
	public function testmailAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$email = 'east_bbk@inbox.ru';
		$password = 'rhbgnj16';
		$this->mailservice->sendRegistrationMail($email,$password);
	}
	*/
	
	public function logoutAction(){
		$this->service->signOut();
		$this->redirect('/');
	}
	
	public function noauthAction(){
		$this->view->pageTitle = 'Идентификация пользователя';
		$navItem = $this->view->navigation()->findOneById('user-no-auth');
		if($navItem){
			$navItem->setActive('true');
		}
	}
	
	public function vkAuthAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$appCookie = $_COOKIE['vk_app_'.$this->vkAppId];
		
		$session = array();
		$member = FALSE;
		$valid_keys = array('expire', 'mid', 'secret', 'sid', 'sig');
		
		if($appCookie){
			$session_data = explode ('&', $appCookie);
			//var_dump($session_data);
			foreach ($session_data as $pair) {
		      list($key, $value) = explode('=', $pair);
		      if (empty($key) || empty($value) || !in_array($key, $valid_keys)) {
		        continue;
		      }
		      $session[$key] = $value;
		    }
		    ksort($session);
		    $hash = '';
		    foreach($session as $key => $value){
		    	if($key != 'sig'){
		    		$hash .= $key.'='.$value;
		    	}
		    }
		    $hash .= $this->vkAppKey;
		    $response = array('result' => 'fail');
		    if(md5($hash) == $session['sig'] && $session['expire'] > time()){
		    	$response['result'] = 'success';
		    	if($this->service->socialUserExist($session['mid'])){
		    		$this->service->autorizeVKUser($session['mid']);
		    	}else{
		    		$this->service->addVKUser($this->_getAllParams(),$session['mid']);
		    	}
		    }
		    echo Zend_Json::encode($response);
		}
	}
	
	public function fbAuthAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		
		FacebookSession::setDefaultApplication($this->fbAppId,$this->fbAppKey);
		
		$helper = new FacebookJavaScriptLoginHelper();
		$response = array('result' => 'fail');
		try {
			$session = $helper->getSession();
			$me = (new FacebookRequest(
				$session, 'GET', '/me'
			))->execute()->getGraphObject(GraphUser::className());
			if(!$this->service->socialUserExist($me->getId())){
				$data = array(
					'fname' => $me->getFirstName(),
					'lname' => $me->getLastName(),
					'mname' => $me->getMiddleName(),
					'bdate' => $me->getBirthday(),
					'email' => $me->getProperty('email'),
					'sex' => $me->getProperty('gender'),
					'social_id' => $me->getId()
				);
				$this->service->addFBUser($data,$me->getId());
			}
			$this->service->autorizeFBUser($me->getId());
			$response['result'] = 'success';
		} catch (FacebookRequestException $e) {
			// The Graph API returned an error
		} catch (\Exception $e) {
			// Some other error occurred
		}
		echo Zend_Json::encode($response);
	}
	
	public function tweetAuthAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$oauthToken = $this->_getParam('oauth_token',false);
		$oauthTokenVerifier = $this->_getParam('oauth_verifier',false);
		
		if($oauthToken && $oauthTokenVerifier){
			$oauth_nonce = md5(uniqid(rand(), true));
			$oauth_timestamp = time();
			
			$session = new Zend_Session_Namespace('tweet_auth');
			
			$oauth_base_text = "GET&";
			$oauth_base_text .= urlencode($this->urlAceessToken)."&";
			$oauth_base_text .= urlencode("oauth_consumer_key=".$this->consumerKey."&");
			$oauth_base_text .= urlencode("oauth_nonce=".$oauth_nonce."&");
			$oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
			$oauth_base_text .= urlencode("oauth_token=".$oauthToken."&");
			$oauth_base_text .= urlencode("oauth_timestamp=".$oauth_timestamp."&");
			$oauth_base_text .= urlencode("oauth_verifier=".$oauthTokenVerifier."&");
			$oauth_base_text .= urlencode("oauth_version=1.0");
			
			//$this->oauthToken = $params['oauth_token'];
			//$this->oauthTokenSecret = $params['oauth_token_secret'];
			
			$key = $this->consumerSecret."&".$session->oauthTokenSecret;
			$oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
			
			
			$url = $this->urlAceessToken;
			$url .= '?oauth_nonce='.$oauth_nonce;
			$url .= '&oauth_signature_method=HMAC-SHA1';
			$url .= '&oauth_timestamp='.$oauth_timestamp;
			$url .= '&oauth_consumer_key='.$this->consumerKey;
			$url .= '&oauth_token='.urlencode($oauthToken);
			$url .= '&oauth_verifier='.urlencode($oauthTokenVerifier);
			$url .= '&oauth_signature='.urlencode($oauth_signature);
			$url .= '&oauth_version=1.0';
			
			$response = file_get_contents($url);
			$params = $this->parseAuthString($response);
			
			if(count($params) == 4 && isset($params['user_id']) && !empty($params['user_id'])){
				//авторизован, получаем данные пользователя
				$oauth_nonce = md5(uniqid(rand(), true));
				$oauth_timestamp = time();
				$oauth_token = $params['oauth_token'];
				$oauth_token_secret = $params['oauth_token_secret'];
				$screen_name = $params['screen_name'];
				
				$oauth_base_text = "GET&";
				$oauth_base_text .= urlencode($this->urlAccountData).'&';
				$oauth_base_text .= urlencode('oauth_consumer_key='.$this->consumerKey.'&');
				$oauth_base_text .= urlencode('oauth_nonce='.$oauth_nonce.'&');
				$oauth_base_text .= urlencode('oauth_signature_method=HMAC-SHA1&');
				$oauth_base_text .= urlencode('oauth_timestamp='.$oauth_timestamp."&");
				$oauth_base_text .= urlencode('oauth_token='.$oauth_token."&");
				$oauth_base_text .= urlencode('oauth_version=1.0&');
				$oauth_base_text .= urlencode('screen_name=' . $screen_name);
				
				$key = $this->consumerSecret . '&' . $oauth_token_secret;
				$signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
				
				$url = $this->urlAccountData;
				$url .= '?oauth_consumer_key=' . $this->consumerKey;
				$url .= '&oauth_nonce=' . $oauth_nonce;
				$url .= '&oauth_signature=' . urlencode($signature);
				$url .= '&oauth_signature_method=HMAC-SHA1';
				$url .= '&oauth_timestamp=' . $oauth_timestamp;
				$url .= '&oauth_token=' . urlencode($oauth_token);
				$url .= '&oauth_version=1.0';
				$url .= '&screen_name=' . $screen_name;
				
				$response = file_get_contents($url);
				
				$user_data = json_decode($response);
				
				if(!$this->service->socialUserExist($user_data->id_str)){
					$fname = '';
					$lname = '';
					$names = explode(' ',$user_data->name);
					if(count($names) == 2){
						$fname = $names[0];
						$lname = $names[1];
					}
					$data = array(
						'fname' => $fname,
						'lname' => $lname,
						'social_id' => $user_data->id_str
					);
					$this->service->addTwitterUser($data);
				}else{
					$this->service->autorizeTwitterUser($user_data->id_str);
				}
				$this->redirect('/profile');
			}
		}else{
			$this->redirect('/user/registration');
		}
	}
	
	protected function parseAuthString($response){
		$parts = explode('&',$response);
		
		$params = array();
		
		if(count($parts) == 4){
			foreach($parts as $index => $part){
				$tmp = explode('=',$part);
				if(count($tmp) == 2){
					$params[$tmp[0]] = $tmp[1];
				}
			}
		}
		return $params;
	}
}