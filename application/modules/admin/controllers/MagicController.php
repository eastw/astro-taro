<?php
class Admin_MagicController extends Zend_Controller_Action{
	
	protected $service;
	
	protected $tags;
	
	protected $navigation;
	
	protected $profileService;
	
	public function preDispatch(){
		$this->service = new App_ArticleService();
		$this->tags = new App_TagService();
		$this->navigation = new App_NavigationService();
		$this->profileService = new App_ProfileService($this->view->userdata);
	}
	
	public function indexAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->service->buildMagicQuery();
		
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	
	public function addAction(){
		$form = new Application_Form_MagicForm();
		$this->view->form = $form;
		$this->view->actionType = 'add';
		$form->image->setRequired(true);
		$form->fillTags($this->tags->getAllMagicTags());
		
		$session = new Zend_Session_Namespace('addmagictag');
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			//var_dump($session->tags); die;
			if($form->isValid($formData)){
				//$cleanValues = $form->getValidValues($formData);
				$adapter = $form->image->getTransferAdapter();
				foreach ($adapter->getFileInfo() as $file) {
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					$newName = uniqid().'.'.$ext;
					$path = realpath(dirname('.')).
					DIRECTORY_SEPARATOR.
					'files'.
					DIRECTORY_SEPARATOR.
					'magic'.
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
				$validData['tags'] = $session->tags;
				unset($session->tags);
				$this->service->addMagic($validData);
				$this->navigation->refreshNavigation();
				$this->redirect('/admin/magic');
			}else{
				$form->fillTagsplace();
				$form->populate($formData);
			}
		}else{
			unset($session->tags);
		}
		$this->render('edit');
	}
	
	public function editAction(){
		$form = new Application_Form_MagicForm();
		$this->view->form = $form;
		$this->view->actionType = 'edit';
		
		$id = $this->_getParam('id',false);
		$article = $this->service->getArticleById($id);
		
		$form->title->setValue($article['title']);
		$form->img_note->setValue('<img src="/files/magic/'.$article['image'].'" />');
		$form->anonse->setValue($article['anonse']);
		$form->content->setValue($article['content']);
		$form->activity->setValue($article['activity']);
		$form->seokeywords->setValue($article['meta_keys']);
		$form->seodescription->setValue($article['meta_desc']);
		$form->fillTags($this->tags->getAllMagicTags());
		
		$page = $this->_getParam('page','');
		$form->getElement('page')->setValue($page);
		
		$session = new Zend_Session_Namespace('addmagictag');
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$validData = $form->getValidValues($formData);
				$validData['image'] = $article['image'];
				$adapter = $form->image->getTransferAdapter();
				foreach ($adapter->getFileInfo() as $file) {
					if(!empty($file['name'])){
						$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
						$newName = uniqid().'.'.$ext;
						$path = realpath(dirname('.')).
						DIRECTORY_SEPARATOR.
						'files'.
						DIRECTORY_SEPARATOR.
						'magic'.
						DIRECTORY_SEPARATOR.
						$newName;
						
						$validData['image'] = $newName;
						$adapter->addFilter('Rename', array(
								'target' => $path,
								'overwrite' => true
						));
						$adapter->receive($file['name']);
						$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'magic'.DIRECTORY_SEPARATOR.$article['image'];
						if(file_exists($realPath)){
							unlink($realPath);
						}
					}
				}
				$validData['tags'] = $session->tags;
				$this->service->saveMagic($validData,$article['id']);
				$this->navigation->refreshNavigation();
				$this->profileService->refreshFavoriteLink($article['id'], 'magic');
				$this->redirect((!empty($page))?'/admin/magic/index/page/'.$page :'/admin/magic');
			}else{
				$form->img_note->setValue('<img src="/files/magic/'.$article['image'].'" />');
				$form->fillTagsplace();
				$form->populate($formData);
			}
		}else{
			unset($session->tags);
			$session->tags = array();
			foreach($article['tags'] as $tag){
				$session->tags[$tag['id']] = $tag['tagname'];
			}
			$form->fillTagsplace();
		}
		$this->render('edit');
	}
	
	public function removeAction(){
		$id = $this->_getParam('id',false);
		if(null !== $id){
			$article = $this->service->getArticleById($id);
			$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'magic'.DIRECTORY_SEPARATOR.$article['image'];
			if(file_exists($realPath)){
				unlink($realPath);
			}
			$this->service->deleteArticle($id);
			$page = $this->_getParam('page','');
			$this->redirect((!empty($page))?'/admin/magic/index/page/'.$page :'/admin/magic');
		}
	}
	
	public function searchAction(){
		$this->_helper->layout->disableLayout();
		$query = $this->_getParam('query','');
		$data = $this->service->searchMagic($query);
		$this->view->data = $data;
		$this->render('search');
		
	}
	
	public function addtagAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$tag_id = $this->_getParam('tag_id');
		$tagname = $this->_getParam('tagname');
		
		$session = new Zend_Session_Namespace('addmagictag');
		if(!isset($session->tags)){
			$session->tags = array();
		}
		if(!array_key_exists($tag_id,$session->tags)){
			$session->tags[$tag_id] = $tagname;
		}
		
	}
	
	public function removetagAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$tag_id = $this->_getParam('tag_id');
		$session = new Zend_Session_Namespace('addmagictag');
		if(!isset($session->tags)){
			$session->tags = array();
		}
		$tags = $session->tags;
		foreach($tags as $index => $tag){
			if($tag_id == $index){
				unset($tags[$index]);
			}
		}
		//var_dump($tags); 
		$session->tags = $tags;
	}
	
	public function postDispatch(){
	
		
		$db = $this->service->getDb();
	
		$profiler = $db->getProfiler();
	
		$profile = '';
		foreach($profiler->getQueryProfiles() as $query) {
			$profile .= $query -> getQuery() . "\n"
					. 'Time: ' . $query -> getElapsedSecs();
		}
	
		echo $profile;
		
	}
	
}