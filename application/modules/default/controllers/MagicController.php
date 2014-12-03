<?php
class MagicController extends App_Controller_Action_ParentController{
	
	protected $profileService;
	
	protected $commentsService;
	
	public function init(){
		$this->profileService = new App_ProfileService($this->view->userdata);
		$this->commentsService = new App_CommentsService();
	}
	
	public function listAction(){
		$page = $this->_getParam('page','');
		$type = $this->_getParam('type',false);
		$typeArray = array('all','popular');
		
		if(in_array($type, $typeArray)){
			$query = $this->articleService->buildMagicAnonseQuery($type);
			$this->view->page = $page;
			$this->view->type = $type;
			$this->view->pageTitle = 'Магия';
			$this->view->topMenuActiveItem = 'magic';
			$this->view->tags = $this->tagService->getCachedMagicTags();
			$this->view->navigation()->findOneById('magic')->setActive('true');
			$paginator = Zend_Paginator::factory($query);
			$paginator->setCurrentPageNumber($page,'');
			$paginator->setItemCountPerPage(15);
			$paginator->setPageRange(7);
			$this->view->paginator = $paginator;
			
			$pages = $this->pagesService->getAllPages();
			$curPage = array();
			$pageUrl = '';
			if($type == 'all'){
				$pageUrl = '/magic';
			}else{
				$pageUrl = '/magic/1/popular';
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
		}
	}
	
	public function tagAction(){
		$page = $this->_getParam('page','');
		
		$tag = $this->_getParam('tag',false);
		
		$tag = $this->tagService->getTagByAlias($tag,$this->view->magictags);
		
		$curpage = $this->view->navigation()->findOneById($tag['alias']);//->setActive('true');
		if(null !== $curpage){
			$curpage->setActive(true);
		}
		
		$this->view->tag = $tag;
		
		$this->view->tags = $this->tagService->getCachedMagicTags();
		
		$query = $this->articleService->buildTagMagicQuery($tag['id']);
		
		$this->view->page = $page;
		
		$this->view->topMenuActiveItem = 'magic';
		
		$this->view->pageTitle = $tag['tagname'];
		
		$this->view->seotitle = 'Магия — '.$tag['tagname'];
		$this->view->seokeywords = $tag['seo-keywords'];
		$this->view->seodescription = $tag['seo-description'];
		if($page != '1' && is_numeric($page)){
			$this->view->seotitle .= '::Страница '.$page;
		}
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(15);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
		$this->render('taglist');
	}
	
	public function contentAction(){
		//echo 'here'; die;
		$alias = $this->_getParam('alias',false);
		$tag = $this->_getParam('tag',false);
		
		$tag = $this->tagService->getTagByAlias($tag,$this->view->magictags);
		$article = $this->articleService->getArticleByAlias($alias);//ArticleById($id);
		
		$this->view->topMenuActiveItem = 'magic';
		$this->view->pageTitle = $article['title'];
		$this->view->tag = $tag;
		
		$this->view->tags = $this->tagService->getCachedMagicTags();
		
		if(isset($this->view->userdata) && !empty($this->view->userdata)){
			$article['is_favorite'] = $this->profileService->isFavorite($article['id'], 'magic', $this->view->userdata->id);
		}
		
		//var_dump($tag); die;
		
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
		$this->view->seotitle = 'Магия «'.$article['title'].'»';
		$this->view->seokeywords = $article['meta_keys'];
		$this->view->seodescription = $article['meta_desc'];
		
		$this->view->article = $article;
		
		$this->view->attributes = array(
			'type' => 'magic',
			'subtype' => '',
			'sign' => '',
			'resource_id' => $article['id'] 
		);
		
		$this->view->comments = $this->commentsService->getComments('magic', '', '', $article['id']);
	}
	
	
	public function postDispatch(){
		parent::postDispatch();
	}
	
	
}