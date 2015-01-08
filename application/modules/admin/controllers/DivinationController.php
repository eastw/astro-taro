<?php
class Admin_DivinationController extends Zend_Controller_Action{
	
	protected $service;
	
	protected $deckService;
	
	protected $categoryService;
	
	protected $navigation;
	
	protected $profileService;
	
	public function preDispatch(){
		$this->service = new App_DivinationService();
		$this->deckService = new App_DeckService();
		$this->categoryService = new App_CategoryService();
		$this->navigation = new App_NavigationService();
		$this->profileService = new App_ProfileService($this->view->userdata);
	}
	
	public function deckAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->deckService->listDecksQuery();
		
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function addDeckAction(){
		$this->view->actionType = 'add';
		$form = new Application_Form_DeckForm();
		$this->view->form = $form;
		$form->fillTypes($this->categoryService->getCategoryTypes());
		if($this->getRequest()->isPost()){
			$data = $this->_getAllParams();
			if($form->isValid($data)){
				$validValues = $form->getValidValues($data);
				$deckFolder = App_UtilsService::generateTranslit($validValues['title']);
				mkdir(realpath(dirname('.')) . DIRECTORY_SEPARATOR . 'files' .
						DIRECTORY_SEPARATOR . 'decks' . DIRECTORY_SEPARATOR . $deckFolder);
				$adapter = $form->back->getTransferAdapter();
				$files = array();
				$path = realpath(dirname('.')) . DIRECTORY_SEPARATOR.
									'files' . DIRECTORY_SEPARATOR . 'decks' . DIRECTORY_SEPARATOR.
									$deckFolder . DIRECTORY_SEPARATOR;
				foreach ($adapter->getFileInfo() as $file) { 
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					$newName = uniqid() . '.' . $ext;
					$filepath = $path . $newName;
					$files[] = $newName;
					$adapter->addFilter('Rename', array(
							'target' => $filepath,
							'overwrite' => true
					));
					$adapter->receive($file['name']);
				}
				$validValues['back'] = $files[0];
				$validValues['reshuffle'] = $files[1];
				$this->deckService->addDeck($validValues);
				$this->redirect('/admin/divination/deck');
			}else{
				$form->populate($data);
			}
		}
		$this->render('edit-deck');
	}
	
	public function editDeckAction(){
		$this->view->actionType = 'edit';
		$id = $this->_getParam('id',false);
		$page = $this->_getParam('page','');
		if($id){
			$deck = $this->deckService->getDeckById($id);
			$form = new Application_Form_DeckForm();
			$form->title->setValue($deck['name']);
			$form->back_note->setValue('<img src="/files/decks/' . $deck['folder_alias'] . '/' . $deck['back'] . '">');
			$form->reshuffle_note->setValue('<img src="/files/decks/' . $deck['folder_alias'] . '/' . $deck['reshuffle'] . '">');
			$form->fillTypes($this->categoryService->getCategoryTypes());
			$form->type->setValue($deck['type_id']);
			$this->view->form = $form;
			
			if($this->getRequest()->isPost()){
				$data = $this->_getAllParams();
				if($form->isValid($data)){
					$validData = $form->getValidValues($data);
					$path = realpath(dirname('.')) . DIRECTORY_SEPARATOR.
									'files' . DIRECTORY_SEPARATOR . 'decks' . DIRECTORY_SEPARATOR.
									$deck['folder_alias'] . DIRECTORY_SEPARATOR;
					$files = array();
					$adapter = $form->back->getTransferAdapter();
					foreach ($adapter->getFileInfo() as $index => $file) {
						if(!empty($file['name'])){
							$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
							$newName = uniqid() . '.' . $ext;
							$filepath = $path.$newName;
							$files[$index] = $newName;
							$adapter->addFilter('Rename', array(
									'target' => $filepath,
									'overwrite' => true
							));
							$adapter->receive($file['name']);
						}
					}
					foreach($files as $index => $item){
						if(isset($files[$index])){
							if(file_exists($path.$deck[$index])){
								unlink($path.$deck[$index]);
							}
							$validData[$index] = $files[$index];
						}
					}
					if($deck['name'] != $validData['title']){
						rename($path, realpath(dirname('.')) . DIRECTORY_SEPARATOR.
									'files' . DIRECTORY_SEPARATOR . 'decks' . DIRECTORY_SEPARATOR.
									App_UtilsService::generateTranslit($validData['title'])
									.DIRECTORY_SEPARATOR);
					}
					$this->deckService->saveDeck($validData,$deck['id']);
					$this->redirect((!empty($page)) ? '/admin/divination/deck/page/' . $page : '/admin/divination/deck');
				}else{
					$form->back_note->setValue('<img src="/files/decks/' . $deck['folder_alias'] . '/' . $deck['back'] . '">');
					$form->reshuffle_note->setValue('<img src="/files/decks/' . $deck['folder_alias'] . '/' . $deck['reshuffle'] . '">');
					$form->populate($data);
				}
			}
		}
	}
	
	public function removeDeckAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id','');
		if($id){
			$this->deckService->removeDeck($id);
		}
		$page = $this->_getParam('page','');
		$this->redirect((!empty($page)) ? '/admin/divination/deck/page/' . $page : '/admin/divination/deck');
	}
	
	public function cardsDeckAction(){
		$id = $this->_getParam('id',false);
		if($id){
			$deck = $this->deckService->getDeckById($id);
			$this->view->path = realpath(dirname('.')) . DIRECTORY_SEPARATOR.
									'files' . DIRECTORY_SEPARATOR . 'decks' . DIRECTORY_SEPARATOR.
									$deck['folder_alias'] . DIRECTORY_SEPARATOR;
			$this->view->deck =$deck;
			$this->view->cardsCount = 0;
			switch($deck['type']){
				case 'taro': $this->view->cardsCount = 78; break;
				case 'classic': $this->view->cardsCount = 36; break;
				case 'lenorman': $this->view->cardsCount = 36; break;
				case 'rune': $this->view->cardsCount = 24; break;
			}
			
			$images = array();
			for($i = 0, $n = $this->view->cardsCount; $i < $n; $i++){
				$item = array();
				if(file_exists($this->view->path . $i . '.jpg')){
					$item['normal'] = $i . '.jpg';
				}
				if(file_exists($this->view->path . $i . '.png')){
					$item['normal'] = $i . '.png';
				}
				
				if(file_exists($this->view->path . $i . '.gif')){
					$item['normal'] = $i . '.gif';
				}
				
				if(file_exists($this->view->path . $i . '_0.jpg')){
					$item['reverse'] = $i . '_0.jpg';
				}
				
				if(file_exists($this->view->path . $i . '_0.png')){
					$item['reverse'] = $i . '_0.png';
				}
				if(file_exists($this->view->path . $i . '_0.gif')){
					$item['reverse'] = $i . '_0.gif';
				}
				
				$images[] = $item;
			}
			$this->view->images = $images;
		}
	}
	
	public function changeDeckActivityAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$json = array();
		$json = $this->deckService->changeDeckActivity($this->_getParam('id'));
		echo Zend_Json::encode($json);
	}
	
	public function saveDeckCardAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$json = array();
		if($this->getRequest()->isPost()){
			$data = $this->_getAllParams();
			$number = $data['number'];
			$id = $data['id'];
			$deck = $this->deckService->getDeckById($id);
			$acceptTypes = array('image/jpeg','image/png');
			$files = array();
			$valid = true;
			$errors = array();
			$path = realpath(dirname('.')) . DIRECTORY_SEPARATOR.
								'files' . DIRECTORY_SEPARATOR . 'decks' . DIRECTORY_SEPARATOR.
								$deck['folder_alias'] . DIRECTORY_SEPARATOR;
			$json['id'] = $deck['id'];
			$json['number'] = $number;
			if(!count($_FILES)){
				$json['result'] = 'fail';
				$json['errors']['empty']['emptyFiles'] = 'Ни один файл не был выбран при загрузке';
			}
			foreach($_FILES as $index => $file){
				if(!in_array($file['type'], $acceptTypes)){
					$json['result'] = 'fail';
					$json['errors'][$index]['incorrectType'] = 'Тип файла должен быть jpg или png';
					$valid = false; 
				}
				if($file['size'] > 15000000){
					$json['result'] = 'fail';
					$json['errors'][$index]['incorrectSize'] = 'Файл слишком большой для карты, попробуйте сделать его меньше';
					$valid = false;
				}
				if($valid){
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					$newName = '';
					if($index == 'normal'){
						$newName = $number.'.'.$ext;
						if(file_exists($path.$number.'.jpg')){
							unlink($path.$number.'.jpg');
						}
						if(file_exists($path.$number.'.png')){
							unlink($path.$number.'.png');
						}
						if(file_exists($path.$number.'.gif')){
							unlink($path.$number.'.gif');
						}
					}elseif($index == 'reverse'){
						$newName = $number.'_0.'.$ext;
						if(file_exists($path.$number.'_0.jpg')){
							unlink($path.$number.'_0.jpg');
						}
						if(file_exists($path.$number.'_0.png')){
							unlink($path.$number.'_0.png');
						}
						if(file_exists($path.$number.'_0.gif')){
							unlink($path.$number.'_0.gif');
						}
					}
					if(!empty($newName)){
						$files[$index] = $newName;
						move_uploaded_file($file['tmp_name'], $path.$newName);
					}
				}
			}
			if(count($files)){
				$json['result'] = 'success';
				$json['files'] = $files;
				$json['folder'] = $deck['folder_alias'];
			}else{
				
			}
			
		}
		echo Zend_Json::encode($json);
	}
	
	public function taroAction(){
		$page = $this->_getParam('page','');
		
		$query = $this->service->listDivinationsQuery();//buildArticlesQuery();
		//var_dump($query->assemble()); die;
		$this->view->page = $page;
		
		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($page,'');
		$paginator->setItemCountPerPage(50);
		$paginator->setPageRange(7);
		$this->view->paginator = $paginator;
	}
	
	public function addTaroAction(){
		$this->view->actionType = 'add';
		$form = new Application_Form_DivinationForm();
		$this->view->form = $form;
		//$this->view->form->fillCategories($this->categoryService->flatCategories());
		//$this->view->form->fillDecks($this->deckService->listDecks());
		$types = $this->categoryService->getCategoryTypes();
		$this->view->form->fillTypes($types);
		$session = new Zend_Session_Namespace('adddeck');
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$adapter = $form->image->getTransferAdapter();
				$files = array();
				foreach ($adapter->getFileInfo() as $index => $file) {
					//var_dump($index); 
					$validData = $form->getValidValues($formData);
					if(!empty($file['name'])){
						$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
						$newName = uniqid().'.'.$ext;
						$files[$index] = $newName;
						$path = realpath(dirname('.')).
						DIRECTORY_SEPARATOR.
						'files'.
						DIRECTORY_SEPARATOR.
						'divinations'.
						DIRECTORY_SEPARATOR.
						$newName;
					
						$adapter->addFilter('Rename', array(
								'target' => $path,
								'overwrite' => true
						));
						$adapter->receive($file['name']);
					}else{
						$files[$index] = '';
					}
				}
				//var_dump($files); die;
				$validData['background'] = $files['image'];
				$validData['image'] = $files['image2'];
				$validData['alignment_form'] = $files['alignment_form'];
				$validData['front_background'] = $files['front_background'];
				
				$validData['decks'] = $session->decks;
				unset($session->decks);
				$this->service->addDivination($validData);
				$this->navigation->refreshNavigation();
				$this->redirect('/admin/divination/taro');
			}else{
				$this->view->form->fillDecks($this->deckService->getDecksByType($formData['type']));
				if($formData['type'] < 4 || $formData['type'] == 6){
					$this->view->form->fillCategories($this->categoryService->getChildCategoriesByType($formData['type']));
				}else{
					$this->view->form->fillCategories($this->categoryService->getRootCategoriesByType($formData['type']));
				}
				$form->populate($formData);
				$form->fillDecksplace();
			}
		}else{
			$this->view->form->fillCategories($this->categoryService->getChildCategoriesByType($types[0]['id']));
			$this->view->form->fillDecks($this->deckService->getDecksByType($types[0]['id']));
			unset($session->decks);
		}
		$this->render('edit-taro');
	}
	
	public function addTaroDeckAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$deck_id = $this->_getParam('deck_id');
		$deckname = $this->_getParam('deckname');
		
		$session = new Zend_Session_Namespace('adddeck');
		if(!isset($session->decks)){
			$session->decks = array();
		}
		if(!array_key_exists($deck_id,$session->decks)){
			$session->decks[$deck_id] = $deckname;
		}
	}
	
	public function removeTaroDeckAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	
		$deck_id = $this->_getParam('deck_id');
		$session = new Zend_Session_Namespace('adddeck');
		if(!isset($session->decks)){
			$session->decks = array();
		}
		$decks = $session->decks;
		foreach($decks as $index => $deck){
			if($deck_id == $index){
				unset($decks[$index]);
			}
		}
		$session->decks = $decks;
	}
	
	public function getDecksAndCategoriesByTypeAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$typeId = $this->_getParam('type',false);
		if($typeId){
			$json = array('decks' => array(),'categories' => array());
			$types = $this->categoryService->getCategoryTypes();
			$type = '';
			foreach($types as $item){
				if($typeId == $item['id']){
					$type = $item['type'];
				}
			}
			if($type == 'taro' || $type == 'classic' || $type == 'rune' || $type == 'lenorman'){
				$json['decks'] = $this->deckService->getDecksByType($typeId);
				$json['categories'] = $this->categoryService->getChildCategoriesByType($typeId);
				$session = new Zend_Session_Namespace('adddeck');
				unset($session->decks);
			}
			if($type == 'book' || $type == 'other'){
				$json['decks'] = array();
				$json['categories'] = $this->categoryService->getRootCategoriesByType($typeId);
			}
			echo Zend_Json::encode($json);
		}
	}
	
	public function editTaroAction(){
		$this->view->actionType = 'edit';
		$form = new Application_Form_DivinationForm();
		$id = $this->_getParam('id',false);
		$page = $this->_getParam('page',false);
		if($id){
			$types = $this->categoryService->getCategoryTypes();
			$form->fillTypes($types);
			$divination = $this->service->getDivinationById($id);
			$form->type->setValue($divination['type_id']);
			
			if($divination['type'] == 'book' || $divination['type'] == 'other'){
				$form->fillCategories($this->categoryService->getRootCategoriesByType($divination['type_id']));
			}else{
				$form->fillCategories($this->categoryService->getChildCategoriesByType($divination['type_id']));
			}
			$form->fillDecks($this->deckService->getDecksByType($divination['type_id']));
			
			$form->title->setValue($divination['name']);
			$form->img_note->setValue('<a href="/files/divinations/'.$divination['background'].'" target="_blank"><img style="width:100px" src="/files/divinations/'.$divination['background'].'" /></a>');
			$form->img_note2->setValue('<a href="/files/divinations/'.$divination['image'].'" target="_blank"><img style="width:100px" src="/files/divinations/'.$divination['image'].'" /></a>');
			$form->img_note3->setValue('<a href="/files/divinations/'.$divination['alignment_form'].'" target="_blank"><img style="width:100px" src="/files/divinations/'.$divination['alignment_form'].'" /></a>');
			$form->img_note4->setValue('<a href="/files/divinations/'.$divination['front_background'].'" target="_blank"><img style="width:100px" src="/files/divinations/'.$divination['front_background'].'" /></a>');
			$form->category->setValue($divination['category_id']);
			$form->only_old_arkans->setValue($divination['only_old_arkans']);
			$form->desc->setValue($divination['description']);
			$form->seokeywords->setValue($divination['seokeywords']);
			$form->seodescription->setValue($divination['seodescription']);
			$form->cards->setValue($divination['cards_in_alignment']);
			$form->significators->setValue($divination['significators']);
			$form->matches->setValue($divination['matches']);
			$form->image->setRequired(false);
			$form->image2->setRequired(false);

			$form->alignment_form->setRequired(false);
			$this->view->form = $form;
			//$form->fillDecks($this->deckService->listDecks());
			$session = new Zend_Session_Namespace('adddeck');
			if($this->getRequest()->isPost()){
				$data = $this->_getAllParams();
				if($form->isValid($data)){
					$validData = $form->getValidValues($data);
					$adapter = $form->image->getTransferAdapter();
					$files = array();
					foreach ($adapter->getFileInfo() as $index => $file) {
						if(!empty($file['name'])){
							$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
							$newName = uniqid().'.'.$ext;
							$path = realpath(dirname('.')).
							DIRECTORY_SEPARATOR.
							'files'.
							DIRECTORY_SEPARATOR.
							'divinations'.
							DIRECTORY_SEPARATOR.
							$newName;
							
							$files[$index] = $newName;
							$adapter->addFilter('Rename', array(
									'target' => $path,
									'overwrite' => true
							));
							$adapter->receive($file['name']);
							if($index == 'image'  && !empty($divination['background'])){
								$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'divinations'.DIRECTORY_SEPARATOR.$divination['background'];
								if(file_exists($realPath)){
									unlink($realPath);
								}
							}
							if($index == 'image2'  && !empty($divination['image']) ){
								$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'divinations'.DIRECTORY_SEPARATOR.$divination['image'];
								if(file_exists($realPath)){
									unlink($realPath);
								}
							}
							if($index == 'alignment_form'  && !empty($divination['alignment_form']) ){
								$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'divinations'.DIRECTORY_SEPARATOR.$divination['alignment_form'];
								if(file_exists($realPath)){
									unlink($realPath);
								}
							}
							if($index == 'front_background'  && !empty($divination['front_background']) ){
								$realPath = realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'divinations'.DIRECTORY_SEPARATOR.$divination['front_background'];
								if(file_exists($realPath)){
									unlink($realPath);
								}
							}
						}else{
							$files[$index] = '';
						}
					}
					//var_dump($files); die;
					if(!empty($files['image'])){
						$validData['background'] = $files['image'];
					}
					if(!empty($files['image2'])){
						$validData['image'] = $files['image2'];
					}
					if(!empty($files['alignment_form'])){
						$validData['alignment_form'] = $files['alignment_form'];
					}
					if(!empty($files['front_background'])){
						$validData['front_background'] = $files['front_background'];
					}
					$validData['decks'] = $session->decks;
					$this->service->saveDivination($validData,$divination['id']);
					$this->navigation->refreshNavigation();
					$this->profileService->refreshFavoriteLink($divination['id'], 'divination');
					$this->redirect((!empty($page))?'/admin/divination/taro/page/'.$page :'/admin/divination/taro');
				}else{
					if($divination['type'] == 'book' || $divination['type'] == 'other'){
						$form->fillCategories($this->categoryService->getRootCategoriesByType($data['type']));
					}else{
						$form->fillCategories($this->categoryService->getChildCategoriesByType($data['type']));
					}
					$form->fillDecks($this->deckService->getDecksByType($data['type']));
					$form->img_note->setValue('<a href="/files/divinations/'.$divination['background'].'" target="_blank"><img style="width:100px" src="/files/divinations/'.$divination['background'].'" /></a>');
					$form->img_note2->setValue('<a href="/files/divinations/'.$divination['image'].'" target="_blank"><img style="width:100px" src="/files/divinations/'.$divination['image'].'" /></a>');
					$form->img_note3->setValue('<a href="/files/divinations/'.$divination['alignment_form'].'" target="_blank"><img style="width:100px" src="/files/divinations/'.$divination['alignment_form'].'" /></a>');
					$form->fillDecksplace();
					$form->populate($data);
				}
			}else{
				unset($session->decks);
				$session->decks = array();
				$decks = $this->deckService->getDecksByDivination($divination['id']);
				foreach($decks as $deck){
					$session->decks[$deck['id']] = $deck['name'];
				}
				$form->fillDecksplace();
			}
		}
	}
	
	public function removeTaroAction(){
		$id = $this->_getParam('id',false);
		if(null !== $id){
			$this->service->deleteDivination($id);
			$page = $this->_getParam('page','');
			$this->redirect((!empty($page))?'/admin/divination/taro/page/'.$page :'/admin/divination/taro');
		}
	}
	
	public function searchTaroAction(){
		$this->_helper->layout->disableLayout();
		$query = $this->_getParam('query','');
		$data = $this->service->searchDivination($query);
		$this->view->data = $data;
		$this->render('search');
	}
	
	public function searchDeckAction(){
		$this->_helper->layout->disableLayout();
		$query = $this->_getParam('query','');
		$data = $this->deckService->searchDeck($query);
		$this->view->data = $data;
		$this->render('search-deck');
	}
	
	public function dataTaroAction(){
		$id = $this->_getParam('id',false);
		if($id){
			$this->view->divination = $this->service->getDivinationById($id);
			$this->view->data = $this->service->divinationDataIds($id);
			$decks = $this->deckService->listDecks($this->view->divination['type']);
			$this->view->deck = $decks[0];
			$this->view->types = $this->categoryService->getCategoryTypes();
		}
	}
	
	public function categoryByTypeAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$typeId = $this->getParam('type_id',false);
		if($typeId){
			echo Zend_Json::encode($this->categoryService->getChildCategoriesByType($typeId));
		}
	}
	public function divinationsByCategoryAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$categoryId = $this->getParam('category_id',false);
		if($categoryId){
			echo Zend_Json::encode($this->service->getDivinationsByCategory($categoryId));
		}
	}
	public function copyDivinationDescriptionsAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$fromDivination = $this->_getParam('from_divination',false);
		$toDivination = $this->_getParam('to_divination',false);
		if($fromDivination && $toDivination){
			echo Zend_Json::encode($this->service->copyDivinationDescriptions($fromDivination,$toDivination));
		}
	}
	public function copyDivinationMatchesAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$fromDivination = $this->_getParam('from_divination',false);
		$toDivination = $this->_getParam('to_divination',false);
		if($fromDivination && $toDivination){
			echo Zend_Json::encode($this->service->copyDivinationMatches($fromDivination,$toDivination));
		}
	}
	
	public function getDataTaroAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		$number = $this->_getParam('number',false);
		if($id){
			$data = $this->service->divinationDataIds($id);
			$curdata = array();
			foreach($data as $item){
				if($item['deck_position'] == $number){
					$curdata = $this->service->getDivinationDataById($item['id']);
					break;
				}
			}
			echo Zend_Json::encode($curdata);
		}
	}
	
	public function saveDataTaroAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		$number = $this->_getParam('number',false);
		if($id){
			$data = $this->service->divinationDataIds($id);
			$curdata = array();
			foreach($data as $item){
				if($item['deck_position'] == $number){
					$curdata = $this->service->getDivinationDataById($item['id']);
					break;
				}
			}
			$curdata['title'] = $this->_getParam('title',false);
			$curdata['title_reverse'] = $this->_getParam('title_reverse',false);
			
			$curdata['description'] = $this->_getParam('description',false);
			$curdata['description_reverse'] = $this->_getParam('description_reverse',false);
			$this->service->saveDivinationDataItem($curdata);
		}
	}
	
	public function netTaroAction(){
		$id = $this->_getParam('id',false);
		if($id){
			$this->view->divination = $this->service->getDivinationById($id);
			$this->view->cards = $this->service->getCardsByDivinationId($id);
			$cards_full = 0;
			foreach($this->view->cards as $card){
				if($card['alignment_position'] != 0){
					$cards_full++;
				}
			}
			$this->view->cards_full = $cards_full;
		}
	}
	
	public function saveCardInNetAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$div_id = $this->_getParam('divination_id',false);
		if($div_id){
			$column = $this->_getParam('column',false);
			$row = $this->_getParam('row',false);
			$order = $this->_getParam('order',false);
			$json = array('errors' => array());
			$participation = $this->_getParam('participation',false);
			
			$is_significator = $this->_getParam('is_significator',false);
			$significator_type = $this->_getParam('significator_type',false);
			$cards = $this->service->getCardsByDivinationId($div_id);
			$divination = $this->service->getDivinationById($div_id);
			$card_id = 0; $updated = false; $sign_count = 0;
			
			foreach ($cards as $card){
				if($card['is_significator'] == 'y'){
					$sign_count++;
				}
			}
			if( $sign_count == $divination['significators'] && $is_significator == 1){
				$signFound = false;
				foreach($cards as $index => $card){
					if($column == $card['net_column'] && $row == $card['net_row']){
						$signFound = true;
					}
				}
				if(!$signFound){
					$json['errors'][] = 'Количество сигнификаторов превышает заданное';
				}
			}
			if( $is_significator == 1){
				foreach($cards as $index => $card){
					if($card['significator_type'] == $significator_type && ($column != $card['net_column'] || $row != $card['net_row']) ){
						$json['errors'][] = 'Сигнификатор такого типа уже есть в наборе';
						break;
					}
				}
			}
			$error_count = count($json['errors']);
			foreach($cards as $index => $card){
				if($order && $order == $card['alignment_position'] && ($card['net_column'] != $column || $card['net_row'] != $row)){
					$json['errors'][] = 'Карта с номером '.$order.' уже есть в раскладе';
					$error_count ++;
				}
				if(($card['net_column'] == $column && $card['net_row'] == $row) && $error_count == 0){
					$cards[$index] = $this->service->updateDivinationNetItemById($this->_getAllParams(), $card['id']);
					$updated = true;
				}
			}
			if(!count($json['errors']) && !$updated){
				foreach($cards as $index => $card){
					if($card['alignment_position'] == 0){
						$card_id = $card['id'];
						break;
					}
				}
				if($card_id){
					$cards[$index] = $this->service->updateDivinationNetItemById($this->_getAllParams(), $card_id);
				}
			}
			$json['data'] = $cards;
			echo Zend_Json::encode($json);
		}
	}
	
	public function resetAlignmentAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$div_id = $this->_getParam('divination_id',false);
		if($div_id){
			$this->service->resetDivinationNet($div_id);
		}
	}
	
	public function changeTaroActivityAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		if($id && $this->getRequest()->isPost()){
			$result = $this->service->changeDivinationActivity($id);
			echo Zend_Json::encode($result);
		}
	}
	
	public function bookDescriptionsAction(){
		$id = $this->_getParam('id',false);
		if($id){
			$divination = $this->service->getDivinationById($id);
			$this->view->divination = $divination; 
			$this->view->bookDescriptions = $this->service->getBookDescriptionsByDivinationId($id);
		}
	}
	
	public function saveBookDescriptionImageAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$json = array();
		if($this->getRequest()->isPost()){
			$divId = $this->_getParam('divination_id',false);
			if($divId){
				$order = $this->_getParam('order',false);
				$divination = $this->service->getDivinationById($divId);
				$bookDescriptionItem = $this->service->getBookDescriptionItem($divId, $order);
				$acceptTypes = array('image/jpeg','image/png');
				$files = array();
				$valid = true;
				$errors = array();
				$path = realpath(dirname('.')).DIRECTORY_SEPARATOR.
							'files'.DIRECTORY_SEPARATOR.'decks'.DIRECTORY_SEPARATOR.
							App_UtilsService::generateTranslit($divination['name']).DIRECTORY_SEPARATOR;
				$json['order'] = $order-1;
				if(!count($_FILES)){
					$json['result'] = 'fail';
					$json['errors']['emptyFile'] = 'Ни один файл не был выбран при загрузке';
				}else{
					foreach($_FILES as $index => $file){
						if(!in_array($file['type'], $acceptTypes)){
							$json['result'] = 'fail';
							$json['errors']['incorrectType'] = 'Тип файла должен быть jpg или png';
							$valid = false;
						}
						if($file['size'] > 15000000){
							$json['result'] = 'fail';
							$json['errors']['incorrectSize'] = 'Файл слишком большой для карты, попробуйте сделать его меньше';
							$valid = false;
						}
						if($valid){
							if(!empty($bookDescriptionItem['image']) ){
								if(file_exists($path.$bookDescriptionItem['image'])){
									unlink($path.$bookDescriptionItem['image']);
								}
							}
							$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
							$newName = '';
							$newName = uniqid().'.'.$ext;
							move_uploaded_file($file['tmp_name'], $path.$newName);
							
							$bookDescriptionItem['image'] = $newName;
							
							$this->service->updateBookDescriptionItem($bookDescriptionItem);
							$json['result'] = 'success';
							$json['errors'] = array();
							$json['file'] = $newName;
						}
					}
				}
				echo Zend_Json::encode($json);
			}
		}
	}
	
	public function getBookDescriptionAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$divId = $this->_getParam('divination_id',false);
		$order = $this->_getParam('order',false);
		if($this->getRequest()->isPost() && $divId && $order){
			echo Zend_Json::encode($this->service->getBookDescriptionItem($divId, $order));
		}
	}
	
	public function saveBookDescriptionTextAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$divId = $this->_getParam('divination_id',false);
		$order = $this->_getParam('order',false);
		$text = $this->_getParam('text',false);
		$title = $this->_getParam('title',false);
		if($this->getRequest()->isPost() && $divId && $order){
			$bookDescriptionItem = $this->service->getBookDescriptionItem($divId, $order);
			$bookDescriptionItem['description'] = $text;
			$bookDescriptionItem['title'] = $title;
			$this->service->updateBookDescriptionItem($bookDescriptionItem);
			//echo Zend_Json::encode($this->service->getBookDescriptionItem($divId, $order));
		}
	}
	
	public function otherDescriptionsAction(){
		$id = $this->_getParam('id',false);
		if($id){
			$divination = $this->service->getDivinationById($id);
			$this->view->divination = $divination;
			$this->view->bookDescriptions = $this->service->getBookDescriptionsByDivinationId($id);
		}
		
	}
	
	public function matchAction(){
		$id =$this->_getParam('id',false);
		if($id){
			$this->view->divinationId = $id;
			$divination = $this->service->getDivinationById($id);
			$this->view->divinationType = $divination['type'];
			$decks = $this->deckService->listDecks($divination['type']);
			$this->view->deck = $decks[0];
			$this->view->types = $this->categoryService->getCategoryTypes();
		}
	}

	public function getMatchAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$id = $this->_getParam('divination_id',false);
		$card = $this->_getParam('card_num',false);
		$nextCard = $this->_getParam('next_card_num',false);

		if($id){
			$data = $this->service->getMatchByCardsAndDivinationId($card, $nextCard, $id);
			$json = array();
			if($data){
				$json['status'] = 'success';
				$json['description'] = $data['description'];
			}else{
				$json['status'] = 'fail';
				$json['description'] = '';
			}
			echo Zend_Json::encode($json);
		}
	}

	public function saveMatchAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$id = $this->_getParam('divination_id',false);

		if($id){
			$this->service->saveMatch($this->_getAllParams());
		}
	}
}
