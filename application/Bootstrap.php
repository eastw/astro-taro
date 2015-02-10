<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initCaching(){
		//memcache
		$frontend= array(
	    	'lifetime' => 3600 * 3,
	    	'automatic_serialization' => true
	    );
	
	    $backend= array(
	    	'servers' =>array(
		        array(
			        'host'   => 'localhost',
			        'port'   => 11211,
			        'weight' => 1
		        )
		    ),
		    'compression' => true
	    );
	
	    $cache = Zend_Cache::factory(
	    	'Core',
	    	'Memcached',
	    	$frontend,
	   	 	$backend
	    );
	    
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
    	Zend_Registry::set('cache', $cache);

		/*
		$frontend= array(
	    	'lifetime' => 86400 * 5,
	    	'automatic_serialization' => true
	    );
	
	    $backend= array(
	    	'cache_dir' => APPLICATION_PATH.'/../tmp',
	    );
	    
	    $cache = Zend_Cache::factory(
	    	'Core',
	    	'File',
	    	$frontend,
	   	 	$backend
	    );
	    Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
    	Zend_Registry::set('cache',$cache);
		*/
	}
	
	protected function _initViewHelpers(){
		$this->bootstrap('view');
		$view = $this->getResource('view');
		
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('content-type','text/html; charset=utf-8');
		
		$view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
		
		//do not add slash in end this string "App/View/Helper" - not working
		$view->addHelperPath('App/View/Helper', 'App_View_Helper');   
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewRenderer->setView($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
	}
	
	protected function _initNavigation(){
		$this->bootstrap('view');
		$view = $this->getResource('view');
		//$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/nav.xml', 'nav');
		$container = new Zend_Navigation($config);
		$view->navigation($container);
		
	}
	
	protected function _initMeta(){
		$this->bootstrap('view');
		$view = $this->getResource('view');
		
		$view->headTitle()->setSeparator('|');
		$view->headTitle('AstroTarot');
		$view->seotitle = '';
		$view->seokeywords = '';
		$view->seodescription = '';
	}
	
 	protected function _initLog()
    {
        if ($this->hasPluginResource("log"))
        {
        	//$r = $this->getPluginResource("log");
           Zend_Registry::set("log", $this->getPluginResource("log")->getLog());
        }
    }
    
    protected function _initLoadAclIni() {
    	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/acl.ini');
    	Zend_Registry::set('acl', $config);
    }
    
    protected function _initAclControllerPlugin() {
    	$this->bootstrap('frontcontroller');
    	$this->bootstrap('loadAclIni');
    
    	$front = Zend_Controller_Front::getInstance();
    
    	$aclPlugin = new App_Controller_Plugin_AclPlugin(new App_Acl());
    
    	$front->registerPlugin($aclPlugin);
    }
    
	//routes working :)
	protected function _initRoutes(){
		$frontController = Zend_Controller_Front::getInstance();
		$router = $frontController->getRouter();
		
		//--------------articles ----------------------------
		$router->addRoute(
				'showArticlesWithPage',
				new Zend_Controller_Router_Route('/statyi/:page/:type', array('module' => 'default','controller' => 'article','action' =>'list','page' => 1,'type' => 'all'))
		);
		$router->addRoute(
				'showArticlesByTagWithPage',
				new Zend_Controller_Router_Route('/statyi/tag/:tag/:page', array('module' => 'default','controller' => 'article','action' =>'tag','tag'=>'tag','page' => '1'))
		);
		$router->addRoute(
				'showPage',
				new Zend_Controller_Router_Route('/statyi/content/:tag/:alias', array('module' => 'default','controller' => 'article','action' =>'content'))
		);
		
		//------------news------------------------------------
		$router->addRoute(
				'showNewsWithPage',
				new Zend_Controller_Router_Route('/news/:page/:type', array('module' => 'default','controller' => 'article','action' =>'newslist','page' => 1,'type' => 'all'))
		);
		$router->addRoute(
				'showNewsByTagWithPage',
				new Zend_Controller_Router_Route('/news/tag/:tag/:page', array('module' => 'default','controller' => 'article','action' =>'newstag','tag'=>'tag','page' => '1'))
		);
		$router->addRoute(
				'showNewsPage',
				new Zend_Controller_Router_Route('/news/content/:tag/:alias', array('module' => 'default','controller' => 'article','action' =>'newscontent'))
		);
		//-----------------magic------------------------------
		$router->addRoute(
				'showMagicWithPage',
				new Zend_Controller_Router_Route('/magic/:page/:type', array('module' => 'default','controller' => 'magic','action' =>'list','page' => 1,'type' => 'all'))
		);
		$router->addRoute(
				'showMagicByTagWithPage',
				new Zend_Controller_Router_Route('/magic/tag/:tag/:page', array('module' => 'default','controller' => 'magic','action' =>'tag','tag'=>'tag','page' => '1'))
		);
		$router->addRoute(
				'showMagicPage',
				new Zend_Controller_Router_Route('/magic/content/:tag/:alias', array('module' => 'default','controller' => 'magic','action' =>'content'))
		);
		//divinations ----------------------------------------
		$router->addRoute(
				'showDivinationsWidthCategories',
					new Zend_Controller_Router_Route('/gadaniya/:divtype', array('module' => 'default','controller' => 'divination','action' =>'div-list'))
		);
		$router->addRoute(
				'showDivinationsByCategory',
				new Zend_Controller_Router_Route('/gadaniya/:divtype/:alias', array('module' => 'default','controller' => 'divination','action' =>'category-divinations'))
		);
		$router->addRoute(
				'showDivination',
				new Zend_Controller_Router_Route('/gadaniya/:divtype/:alias/:divalias', array('module' => 'default','controller' => 'divination','action' =>'divination'))
		);
		$router->addRoute(
				'showDayDescription',
				new Zend_Controller_Router_Route('/gadaniya/day-description/:type', array('module' => 'default','controller' => 'divination','action' =>'day-description'))
		);
		$router->addRoute(
				'divinationVote',
				new Zend_Controller_Router_Route('/gadaniya/vote/', array('module' => 'default','controller' => 'divination','action' =>'vote'))
		);
		//--------- profile ----------------
		$router->addRoute(
				'viewProfileDescription',
				new Zend_Controller_Router_Route('/profile/opisaniye/:type', array('module' => 'default','controller' => 'profile','action' =>'get-profile-description','type' => 'sun'))
		);
		$router->addRoute(
				'viewProfileDayDescription',
				new Zend_Controller_Router_Route('/profile/day-description/:type', array('module' => 'default','controller' => 'profile','action' =>'day-description','type' => 'sun'))
		);
		
		//--------- horoscopes -------------
		$router->addRoute(
				'horoscopeByType',
				new Zend_Controller_Router_Route('/horoscope/:sign/:type', array('module' => 'default','controller' => 'horoscope','action' =>'index','type' => 'list','sign' => 'aries'))
		);
		$router->addRoute(
				'horoscopeKarma',
				new Zend_Controller_Router_Route('/horoscope/get-karma-description', array('module' => 'default','controller' => 'horoscope','action' =>'get-karma-description'))
		);
		$router->addRoute(
				'horoscopeCompability',
				new Zend_Controller_Router_Route('/horoscope/get-compability', array('module' => 'default','controller' => 'horoscope','action' =>'get-compability'))
		);
		$router->addRoute(
				'horoscopeGetSignImage',
				new Zend_Controller_Router_Route('/horoscope/get-sign-image', array('module' => 'default','controller' => 'horoscope','action' =>'get-sign-image'))
		);
		//--------- numerology -------------
		$router->addRoute(
				'numerologyByType',
				new Zend_Controller_Router_Route('/numerology/:bigtype/:smalltype', array('module' => 'default','controller' => 'numerology','action' =>'index','bigtype' => 'list','smalltype' => 'no-smalltype'))
		);
		$router->addRoute(
				'numerologyGetDescription',
				new Zend_Controller_Router_Route('/numerology/get-description', array('module' => 'default','controller' => 'numerology','action' =>'get-description'))
		);
		$router->addRoute(
				'numerologyDayDescription',
				new Zend_Controller_Router_Route('/numerology/day-description', array('module' => 'default','controller' => 'numerology','action' =>'day-description'))
		);
		//--------- moon calendar -------------
		$router->addRoute(
				'moonTodaySmall',
				new Zend_Controller_Router_Route('/moon', array('module' => 'default','controller' => 'moon','action' =>'today-small'))
		);
		$router->addRoute(
				'moonTodayDetail',
				new Zend_Controller_Router_Route('/moon/today', array('module' => 'default','controller' => 'moon','action' =>'today-detail'))
		);
		$router->addRoute(
				'moonDayDetail',
				new Zend_Controller_Router_Route('/moon/day/:dayparam', array('module' => 'default','controller' => 'moon','action' =>'day'))
		);
		$router->addRoute(
				'moonGetDays',
				new Zend_Controller_Router_Route('/moon/get-days', array('module' => 'default','controller' => 'moon','action' =>'get-days'))
		);
		//--------- search -------------
		$router->addRoute(
				'search',
				new Zend_Controller_Router_Route('/search/query/:query/page/:page', array('module' => 'default','controller' => 'search','action' =>'index','query' => '','page' => 1))
		);
		
		//--------- sitemap -------------
		$router->addRoute(
				'sitemap',
				new Zend_Controller_Router_Route('/sitemap', array('module' => 'default','controller' => 'index','action' =>'sitemap'))
		);
		$router->addRoute(
				'payservice',
				new Zend_Controller_Router_Route('/service/:type/:theme', array('module' => 'default','controller' => 'payservice','action' =>'index','service' => 'horoscope','theme' => ''))
		);
		$router->addRoute(
				'payserviceOrder',
				new Zend_Controller_Router_Route('/service/order', array('module' => 'default','controller' => 'payservice','action' =>'send-order'))
		);

		/*-------------sonnik---------------*/
		$router->addRoute(
			'dreamIndex',
			new Zend_Controller_Router_Route('/sonnik/:letter', array('module' => 'default','controller' => 'dream','action' =>'index', 'letter' => 'а'))
		);
		$router->addRoute(
			'dreamTypeAndWord',
			new Zend_Controller_Router_Route('/sonnik/:type/:word', array('module' => 'default','controller' => 'dream','action' =>'word'))
		);
		$router->addRoute(
			'dreamSearch',
			new Zend_Controller_Router_Route('/sonnik/search/:squery/:page', array('module' => 'default','controller' => 'dream','action' =>'search','page' => NULL))
		);
		$router->addRoute(
			'dreamTypeSingle',
			new Zend_Controller_Router_Route('/sonnik/type/:type/:letter', array('module' => 'default','controller' => 'dream','action' =>'type', 'letter' => NULL))
		);
		$router->addRoute(
			'dreamWordSingle',
			new Zend_Controller_Router_Route('/sonnik/word/:word/:type', array('module' => 'default','controller' => 'dream','action' =>'word', 'type' => NULL))
		);
	}
	
}

