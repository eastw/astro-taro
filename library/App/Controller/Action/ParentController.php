<?php
class App_Controller_Action_ParentController extends Zend_Controller_Action{
	
	protected $tagService;
	
	protected $articleService;
	
	protected $categoryService;
	
	protected $numerologyService;
	
	protected $horoscopeService;
	
	protected $pagesService;
	
	protected $commentsService;
	
	protected $bannerService;
	
	protected $layoutService;
	
	protected $payservice;

	protected $pollService;
	
	protected $auth;
	
	protected $consumerKey = '8gNWfu7Dv3Xcp2AaVijFeyBek';
	protected $consumerSecret = 'uRBLJpvj00fKlLGWu00b2uXGYXLotynIHQ1HE9Bg3TEjdl3yBw';
	protected $urlCallback = 'http://astrotarot.ru/user/tweet-auth';
	
	protected $urlRequestToken = 'https://api.twitter.com/oauth/request_token';
	protected $urlAuth = 'https://api.twitter.com/oauth/authorize';
	protected $urlAceessToken = 'https://api.twitter.com/oauth/access_token';
	protected $urlAccountData = 'https://api.twitter.com/1.1/users/show.json';
	
	protected $oauthToken = '';
	protected $oauthTokenSecret = '';
	
	public function preDispatch(){
		$this->tagService = new App_TagService();
		$this->articleService = new App_ArticleService();
		$this->categoryService = new App_CategoryService();
		$this->divinationService = new App_DivinationService();
		$this->numerologyService = new App_NumerologyService();
		$this->horoscopeService = new App_HoroscopeService();
		$this->pagesService = new App_PagesService();
		$this->commentsService = new App_CommentsService();
		$this->bannerService = new App_BannerService();
		
		$this->layoutService = new App_LayoutService();
		
		$this->payservice = new App_PayserviceService();

		$this->pollService = App_PollService::getInstance();
		
		$this->view->categories = $this->categoryService->structuredCategories();
		
		$tags = $this->tagService->getCachedTags();
		if(!$tags){
			$this->tagService->recacheArticleTags();
		}
		$this->view->tags = $this->tagService->getCachedTags();
		
		$newstags = $this->tagService->getCachedNewsTags();
		if(!$newstags){
			$this->tagService->recacheNewsTags();
		}
		$this->view->newstags = $this->tagService->getCachedNewsTags();
		
		$magictags = $this->tagService->getCachedMagicTags();
		if(!$magictags){
			$this->tagService->recacheMagicTags();
		}
		$this->view->magictags = $this->tagService->getCachedMagicTags();
		
		$this->view->signs = $this->horoscopeService->getSunSigns();

		$this->auth = Zend_Auth::getInstance();
		if($this->auth->hasIdentity()){
			$this->view->userdata = $this->auth->getIdentity();
		}else{
			$this->view->userdata = null;
		}
		$this->initDayCards();
		
		$this->view->controllerName = $this->getRequest()->getControllerName();
		$this->view->actionName = $this->getRequest()->getActionName();
		
		$pages = $this->pagesService->getAllPages();
		$curUri = $this->getRequest()->getRequestUri();
		foreach($pages as $page){
			if(App_UtilsService::cleanUrlLastSlash($page['url']) == App_UtilsService::cleanUrlLastSlash($curUri)){
				$this->view->seotitle = $page['title'];
				$this->view->seokeywords = $page['keywords'];
				$this->view->seodescription = $page['description'];
				$this->view->minidesc = $page['minidesc'];
			}
			if($this->view->controllerName == 'moon' 
				&& $this->view->actionName == 'day'
				&& App_UtilsService::cleanUrlLastSlash($page['url']) == '/moon/today'){
				$this->view->seotitle = $page['title'];
				$this->view->seokeywords = $page['keywords'];
				$this->view->seodescription = $page['description'];
				$this->view->minidesc = $page['minidesc'];
			}
		}

		//TODO: need to cache this
		$this->view->sliders = $this->bannerService->getSliderData();
		//TODO: need to cache this
		$this->view->banners = $this->bannerService->getBannersByController($this->view->controllerName);
		

		//TODO: need to cache this + not show this on index page
		$this->view->ratingList = $this->layoutService->getRaitingBlockData();
		
		if(!$this->view->userdata){
			$this->prepareTweeterData();
		}

		if($this->view->controllerName != 'index'){
			$this->view->poll = $this->pollService->getActivePoll();
		}
	}
	
	protected function initDayCards(){
		$taroDay = '';
		$taroDayState = '';
		$runeDay = '';
		$runeDayState = '';
		$hexagrammDay = '';
		$dayStartTime = '';
		if(isset($_COOKIE['day_start_time']) && !empty($_COOKIE['day_start_time'])){
			$startDayMillis = mktime(0,0,1,date('n'),date('j'),date('Y'));
			$resultTime = time() - $_COOKIE['day_start_time'];
			if($resultTime > 86400){
				$taroDay = mt_rand(1,78);
				$taroDayState = mt_rand(0, 1);
				$runeDay = mt_rand(1,24);
				$runeDayState = mt_rand(0, 1);
				$hexagrammDay = mt_rand(1,64);
				$dayStartTime = mktime(0,0,1,date('n'),date('j'),date('Y'));
				
				setcookie('taro_day', $taroDay, time() + 3600*24*3, '/');
				setcookie('taro_day_state', $taroDayState, time() + 3600*24*3, '/');
				setcookie('rune_day', $runeDay, time() + 3600*24*3, '/');
				setcookie('rune_day_state', $runeDayState, time() + 3600*24*3, '/');
				setcookie('hexagramm_day', $hexagrammDay, time() + 3600*24*3, '/');
				setcookie('day_start_time', $dayStartTime, time() + 3600*24*3, '/');
			}else{
				$taroDay = $_COOKIE['taro_day'];
				$taroDayState = $_COOKIE['taro_day_state'];
				$runeDay = $_COOKIE['rune_day'];
				$runeDayState = $_COOKIE['rune_day_state'];
				$hexagrammDay = $_COOKIE['hexagramm_day'];
			}
		}else{
			$taroDay = mt_rand(1,78);
			$taroDayState = mt_rand(0, 1);
			$runeDay = mt_rand(1,24);
			$runeDayState = mt_rand(0, 1);
			$hexagrammDay = mt_rand(1,64);
			$dayStartTime = mktime(0,0,1,date('n'),date('j'),date('Y'));
			
			setcookie('taro_day', $taroDay, time() + 3600*24*3, '/');
			setcookie('taro_day_state', $taroDayState, time() + 3600*24*3, '/');
			setcookie('rune_day', $runeDay, time() + 3600*24*3, '/');
			setcookie('rune_day_state', $runeDayState, time() + 3600*24*3, '/');
			setcookie('hexagramm_day', $hexagrammDay, time() + 3600*24*3, '/');
			setcookie('day_start_time', $dayStartTime, time() + 3600*24*3, '/');
			
		}
		$this->view->taroDay = $taroDay;
		$this->view->taroDayState = $taroDayState;
		$this->view->runeDay = $runeDay;
		$this->view->runeDayState = $runeDayState;
		$this->view->hexagrammDay = $hexagrammDay;
		if(isset($this->view->userdata) && !empty($this->view->userdata)){
		}else{
			$this->view->numberDay = '';
		}
	}

	protected function prepareTweeterData(){
		$oauth_nonce = md5(uniqid(rand(), true));
		$oauth_timestamp = time();
		
		$oauth_base_text = "GET&";
		$oauth_base_text .= urlencode($this->urlRequestToken)."&";
		$oauth_base_text .= urlencode("oauth_callback=".urlencode($this->urlCallback)."&");
		$oauth_base_text .= urlencode("oauth_consumer_key=".$this->consumerKey."&");
		$oauth_base_text .= urlencode("oauth_nonce=".$oauth_nonce."&");
		$oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
		$oauth_base_text .= urlencode("oauth_timestamp=".$oauth_timestamp."&");
		$oauth_base_text .= urlencode("oauth_version=1.0");
		
		$key = $this->consumerSecret."&";
		
		$oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
		
		
		$url = $this->urlRequestToken;
		$url .= '?oauth_callback='.urlencode($this->urlCallback);
		$url .= '&oauth_consumer_key='.$this->consumerKey;
		$url .= '&oauth_nonce='.$oauth_nonce;
		$url .= '&oauth_signature='.urlencode($oauth_signature);
		$url .= '&oauth_signature_method=HMAC-SHA1';
		$url .= '&oauth_timestamp='.$oauth_timestamp;
		$url .= '&oauth_version=1.0';
		
		$response = file_get_contents($url);
		
		if($this->parseResponseString($response)){
			$this->view->twitterUrl = $this->urlAuth.'?oauth_token='.$this->oauthToken;
		}
	}
	
	protected function parseResponseString($response){
		$parts = explode('&',$response);
		
		$params = array();
		
		if(count($parts) == 3){
			foreach($parts as $index => $part){
				$tmp = explode('=',$part);
				if(count($tmp) == 2){
					$params[$tmp[0]] = $tmp[1];
				}
			}
		}
		if(count($params) == 3){
			$this->oauthToken = $params['oauth_token'];
			$this->oauthTokenSecret = $params['oauth_token_secret'];
			
			$session = new Zend_Session_Namespace('tweet_auth');
			$session->oauthToken = $params['oauth_token'];
			$session->oauthTokenSecret = $params['oauth_token_secret'];
			
			return true;
		}
		return false;
	}
	
	public function feedbackAction(){
		
	}
	
	public function postDispatch(){
		$db = $this->articleService->getDb();
		$profiler = $db->getProfiler();
		$profile = '';
		if($profiler->getQueryProfiles()){
			foreach($profiler->getQueryProfiles() as $query) {
				$profile .= "\n\n" . 'Time: ' . $query -> getElapsedSecs() . ' ' . $query -> getQuery();
			}
		}
		echo $profile;
	}
}