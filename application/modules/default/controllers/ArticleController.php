<?php
class ArticleController extends App_Controller_Action_ParentController{
	
	protected $profileService;
	
	
	
	public function init(){
		/*Article service*/
		$this->articleService = new App_ArticleService();

		/*Tags*/
		$this->tagService = new App_TagService();
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

		/*Profile*/
		$this->profileService = new App_ProfileService($this->view->userdata);
	}
	
	public function listAction(){
		$page = $this->_getParam('page','');
		$type = $this->_getParam('type',false);
		
		//var_dump($page); die;
		$typeArray = array('all','popular');
		
		if(in_array($type, $typeArray)){
			$query = $this->articleService->buildArticleAnonseQuery($type);
			
			$this->view->type = $type;
			
			$this->view->page = $page;
			
			$this->view->pageTitle = 'Статьи';
			
			$this->view->topMenuActiveItem = 'article';
			
			$navItem = $this->view->navigation()->findOneById('statyi');
			if($navItem){
				$navItem->setActive('true');
			}
			
			$paginator = Zend_Paginator::factory($query);
			$paginator->setCurrentPageNumber($page,'');
			$paginator->setItemCountPerPage(15);
			$paginator->setPageRange(7);
			$this->view->paginator = $paginator;
			
			$pages = $this->pagesService->getAllPages();
			$curPage = array();
			$pageUrl = '';
			if($type == 'all'){
				$pageUrl = '/statyi';
			}else{
				$pageUrl = '/statyi/1/popular';
			}
			foreach($pages as $item){
				if($item['url'] == $pageUrl){
					$curPage = $item;
				}
			}
			if($curPage){
				$this->view->seotitle = $curPage['title'];
				$this->view->seokeywords = $curPage['keywords'];
				$this->view->seodescription = $curPage['description'];
				if($page != '1' && is_numeric($page)){
					$this->view->seotitle .= '|Страница '.$page;
				}
			}
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	public function newslistAction(){
		$page = $this->_getParam('page','');
		$type = $this->_getParam('type',false);
		$typeArray = array('all','popular');
		
		if(in_array($type, $typeArray)){
			$query = $this->articleService->buildNewsAnonseQuery($type);
		
			$this->view->page = $page;
			
			$this->view->type = $type;
		
			$this->view->pageTitle = 'Новости';
			
			$this->view->tags = $this->tagService->getCachedNewsTags();
			
			$this->view->topMenuActiveItem = 'news';
		
			$navItem = $this->view->navigation()->findOneById('news');//->setActive('true');
			if($navItem){
				$navItem->setActive('true');
			}
		
			$paginator = Zend_Paginator::factory($query);
			$paginator->setCurrentPageNumber($page,'');
			$paginator->setItemCountPerPage(15);
			$paginator->setPageRange(7);
			$this->view->paginator = $paginator;
			
			$pages = $this->pagesService->getAllPages();
			$curPage = array();
			$pageUrl = '';
			if($type == 'all'){
				$pageUrl = '/news';
			}else{
				$pageUrl = '/news/1/popular';
			}
			foreach($pages as $item){
				if($item['url'] == $pageUrl){
					$curPage = $item;
				}
			}
			//var_dump($curPage); die;
			if($curPage){
				$this->view->seotitle = $curPage['title'];
				$this->view->seokeywords = $curPage['keywords'];
				$this->view->seodescription = $curPage['description'];
				if($page != '1' && is_numeric($page)){
					$this->view->seotitle .= '|Страница '.$page;
				}
			}
			$this->view->bannerSubtype = 'news';
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	public function tagAction(){
		$page = $this->_getParam('page','');
		
		$tag = $this->_getParam('tag',false);
		
		$tag = $this->tagService->getTagByAlias($tag,$this->view->tags);
		//var_dump($tag); die;
		
		$curpage = $this->view->navigation()->findOneById($tag['alias']);//->setActive('true');
		if($curpage){
			$curpage->setActive(true);
		}
		$this->view->tag = $tag;
		//var_dump($tag); die;
		
		$this->view->seotitle = 'Статьи — '.$tag['tagname'];
		$this->view->seokeywords = $tag['seo-keywords'];
		$this->view->seodescription = $tag['seo-description'];
		if($page != '1' && is_numeric($page)){
			$this->view->seotitle .= '|Страница '.$page;
		}
		
		$this->view->topMenuActiveItem = 'article';
		
		$query = $this->articleService->buildTagArticlesQuery($tag['id']);
		
		$this->view->page = $page;
		
		$this->view->pageTitle = $tag['tagname'];
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(15);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
		
		$this->render('taglist');
	}
	
	public function newstagAction(){
		$page = $this->_getParam('page','');
	
		$tag = $this->_getParam('tag',false);
	
		$tag = $this->tagService->getTagByAlias($tag,$this->view->newstags);
	
		$curpage = $this->view->navigation()->findOneById($tag['alias']);//->setActive('true');
		if(null !== $curpage){
			$curpage->setActive(true);
		}
		$this->view->tag = $tag;
		$this->view->tags = $this->tagService->getCachedNewsTags();
		
		$this->view->topMenuActiveItem = 'news';
	
		$query = $this->articleService->buildTagNewsQuery($tag['id']);
	
		$this->view->page = $page;
	
		$this->view->pageTitle = $tag['tagname'];
		$this->view->tagname = $tag['tagname'];
		
		$this->view->seotitle = 'Новости — '.$tag['tagname'];
		$this->view->seokeywords = $tag['seo-keywords'];
		$this->view->seodescription = $tag['seo-description'];
		if($page != '1' && is_numeric($page)){
			$this->view->seotitle .= '::Страница '.$page;
		}
		
		$this->view->bannerSubtype = 'news';
	
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(15);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
		$this->render('newstaglist');
	}
	
	public function contentAction(){
		//echo 'here'; die;
		$alias = $this->_getParam('alias',false);
		$tag = $this->_getParam('tag',false);
		
		$tag = $this->tagService->getTagByAlias($tag,$this->view->tags);
		$article = $this->articleService->getArticleByAlias($alias);//ArticleById($id);
		
		if(isset($this->view->userdata) && !empty($this->view->userdata)){
			$article['is_favorite'] = $this->profileService->isFavorite($article['id'], 'article', $this->view->userdata->id);
		}
		//echo '<pre>';
		//var_dump($article); die;
		
		$this->view->topMenuActiveItem = 'article';
		$this->view->pageTitle = $article['title'];
		$this->view->tag = $tag;
		
		$options = array(
			'params' => array(
				'tag' => $tag['alias']
			)
		);
		$navItem1 = $this->view->navigation()->findOneById($tag['alias']);
		if($navItem1){
			$navItem2 = $navItem1->findOneById($article['alias']);
			if($navItem2){
				$navItem2->getParent()->setOptions($options);
				$navItem2->setActive('true');
			}
		}
		$this->view->seotitle = 'Cтатья «'.$article['title'].'»';
		$this->view->seokeywords = $article['meta_keys'];
		$this->view->seodescription = $article['meta_desc'];
		
		$this->view->article = $article;
		
		$this->view->attributes = array(
			'type' => 'article',
			'subtype' => '',
			'sign' => '',
			'resource_id' => $article['id'] 
		);
		
		$this->view->comments = $this->commentsService->getComments('article', '', '', $article['id']);
		
		//echo '<pre>';
		//var_dump($this->view->comments); die;
	}
	
	public function newscontentAction(){
		$alias = $this->_getParam('alias',false);
		$tag = $this->_getParam('tag',false);
	
		$tag = $this->tagService->getTagByAlias($tag,$this->view->newstags);
		$article = $this->articleService->getArticleByAlias($alias);//ArticleById($id);
		
		if(isset($this->view->userdata) && !empty($this->view->userdata)){
			$article['is_favorite'] = $this->profileService->isFavorite($article['id'], 'news', $this->view->userdata->id);
		}
		
		$this->view->tags = $this->tagService->getCachedNewsTags();
		
		$this->view->topMenuActiveItem = 'news';
		$this->view->pageTitle = $article['title'];
		$this->view->tag = $tag;
	
		$options = array(
			'params' => array(
					'tag' => $tag['alias']
			)
		);
		$navItem1 = $this->view->navigation()->findOneById($tag['alias']);
		if($navItem1){
			$navItem2 = $navItem1->findOneById($article['alias']);
			if($navItem2){
				$navItem2->getParent()->setOptions($options);
				$navItem2->setActive('true');
			}
		}
		$this->view->seotitle = 'Новость «'.$article['title'].'»';
		$this->view->seokeywords = $article['meta_keys'];
		$this->view->seodescription = $article['meta_desc'];
		
		$this->view->article = $article;
		
		$this->view->attributes = array(
			'type' => 'news',
			'subtype' => '',
			'sign' => '',
			'resource_id' => $article['id'] 
		);
		
		$this->view->comments = $this->commentsService->getComments('news', '', '', $article['id']);
		$this->view->bannerSubtype = 'news';
	}
	
	
	public function voteAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$id = $this->_getParam('id',false);
		
		if($id && $this->getRequest()->isPost()){
			if(!isset($_COOKIE['vote_'.$id])){
				setcookie('vote_'.$id, 'vote', time() + 3600*24*14, '/');
				$this->articleService->setVote($id);
			}
		}
	}
	
	public function sendTypoAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$mailUtils = new App_MailService();
		$mailUtils->sendTypo($this->_getAllParams());
		
		echo 'sended';
	}
	
	public function postDispatch(){
		parent::postDispatch();
	}
	
	
}