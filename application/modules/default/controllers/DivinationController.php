<?php
class DivinationController extends App_Controller_Action_ParentController{
	
	protected $divinationService;
	
	protected $deckService;
	
	protected $categoryService;
	
	protected $profileService;
	
	public function init(){
		$this->divinationService = new App_DivinationService();
		$this->deckService = new App_DeckService();
		$this->categoryService = App_CategoryService::getInstance();
		$this->profileService = new App_ProfileService($this->view->userdata);
	}
	
	public function divListAction(){
		$divtype = $this->_getParam('divtype',false);
		$divtypes = $this->categoryService->getCategoryTypes();
		$inArray = false; $divtypeId = 0;
		if(count($divtypes)){
			foreach ($divtypes as $item){
				if($item['type'] == $divtype){
					$inArray = true;
					$divtypeId = $item['id'];
				}
			}
		}
		if($divtype && $inArray){
			$navItem = $this->view->navigation()->findOneById($divtype.'-list');
			if($navItem){
				$navItem->setActive('true');
			}
			$this->view->divType = $divtype;
			
			if($divtype == 'taro'){
				$this->view->pageTitle = 'Гадания на картах Таро';
			}
			if($divtype == 'classic'){
				$this->view->pageTitle = 'Гадания на классических картах';
			}
			if($divtype == 'lenorman'){
				$this->view->pageTitle = 'Гадания мадам Ленорман';
			}
			if($divtype == 'rune'){
				$this->view->pageTitle = 'Гадания на Рунах';
			}
			if($divtype == 'book'){
				$this->view->pageTitle = 'Гадания по Книге перемен И-цзин';
			}
			if($divtype == 'other'){
				$this->view->pageTitle = 'Другие гадания';
			}
			
			$this->view->sliderExist = true;
			$this->prepareData($divtype,$divtypeId);
			
			if($divtype != 'book' && $divtype != 'other'){
				$this->view->seotitle = 'Гадания::'.$this->view->data['root-category']['name'];
				$this->view->seokeywords = $this->view->data['root-category']['seo-keywords'];
				$this->view->seodescription = $this->view->data['root-category']['seo-description'];
			}else{
				$this->view->seotitle = 'Гадания::'.$this->view->data['name'];
				$this->view->seokeywords = $this->view->data['seo-keywords'];
				$this->view->seodescription = $this->view->data['seo-description'];
			}
			$this->render($divtype.'-list');
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	public function dayDescriptionAction(){
		$type = $this->_getParam('type',false);
		if(in_array($type,array('taro','rune','hexagramm'))){
			$this->view->dayType = $type;
			$indexService = new App_IndexService();
			if($type == 'taro'){
				$this->view->pageTitle = 'Таро карта дня';
				$this->view->smallPageTitle = 'Ваша карта дня';
				$this->view->taroDayData = $indexService->taroDay($this->view->taroDay,$this->view->taroDayState);
			}
			if($type == 'rune'){
				$this->view->pageTitle = 'Руна дня';
				$this->view->smallPageTitle = 'Ваша руна дня';
				$this->view->runeDayData = $indexService->runeDay($this->view->runeDay,$this->view->runeDayState);
			}
			if($type == 'hexagramm'){
				$this->view->pageTitle = 'Гексаграмма дня';
				$this->view->smallPageTitle = 'Ваша гексаграмма дня';
				$this->view->hexagrammDayData = $indexService->hexagrammDay($this->view->hexagrammDay);
			}
			$navItem = $this->view->navigation()->findOneById($type.'-day');
			if($navItem){
				$navItem->setActive('true');
			}
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	protected function prepareData($divtype,$divId){
		$this->view->data = $this->divinationService->getListDivinationsWithCategories($divtype,$divId);
		
	}
	
	public function categoryDivinationsAction(){
		$divtype = $this->_getParam('divtype',false);
		$alias = $this->_getParam('alias',false);
		$divtypes = $this->categoryService->getCategoryTypes();
		$inArray = false; $divtypeId = 0;
		if(count($divtypes)){
			foreach ($divtypes as $item){
				if($item['type'] == $divtype){
					$inArray = true;
					$divtypeId = $item['id'];
				}
			}
		}
		if($divtype && $alias && $inArray){
			$navItem = $this->view->navigation()->findOneById($divtype.'-'.$alias);
			if($navItem){
				$navItem->setActive('true');
			}
			$this->view->divType = $divtype;
			$this->prepareCategoryData($divtype,$divtypeId,$alias);
			$this->view->seotitle = 'Гадания::'.(App_DivinationService::getDivTypeRu($divtype)).'::'.$this->view->data['name'];
			$this->view->seokeywords = $this->view->data['seo-keywords'];
			$this->view->seodescription = $this->view->data['seo-description'];
			
			$this->render($divtype.'-category');
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
		
	}
	
	protected function prepareCategoryData($divtype,$divId, $alias){
		$data = $this->divinationService->getListDivinationsWithCategories($divtype,$divId);

		$category = null;
		foreach($data as $item){
			if($item['alias'] == $alias){
				$category = $item;
			}
		}
		if(null === $category ){
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
		$this->view->pageTitle = $category['name'];
		$this->view->data = $category;
	}
	
	//view single divination
	public function divinationAction(){
		$divtype = $this->_getParam('divtype',false);
		$alias = $this->_getParam('alias',false);
		$divalias = $this->_getParam('divalias',false);
		
		$divtypes = $this->categoryService->getCategoryTypes();
		$inArray = false; $divtypeId = 0;
		if(count($divtypes)){
			foreach ($divtypes as $item){
				if($item['type'] == $divtype){
					$inArray = true;
					$divtypeId = $item['id'];
				}
			}
		}
		if($divtype && $divalias && $alias && $inArray){
			$divination = $this->divinationService->getDivinationByAlias($divalias);
			if($divination['activity'] == 'n'){
				throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
			}
			if(isset($this->view->userdata)){
				$divination['is_favorite'] = $this->profileService->isFavorite($divination['id'], 'divination', $this->view->userdata->id);
			}else{
				$divination['is_favorite'] = false;
			}

			$this->view->divination = $divination;
			$this->view->pageTitle = $divination['name'];
			
			$this->view->divType = $divtype;
			$decks = $this->deckService->getDecksByDivination($divination['id']);
			foreach($decks as &$deck){
				unset($deck['id']);
			}
			$this->view->decks = $decks;
			$this->view->divinationNet = $this->divinationService->getCardsByDivinationId($divination['id']);
			
			$haveNotParticipatedCards = 'false';
			$notParticipatedCards = 0;
			if($divination['type'] == 'classic' || $divination['type'] == 'lenorman'){
				foreach ($this->view->divinationNet as $index => $card){
					if($card['participation'] == 'n'){
						$haveNotParticipatedCards = 'true';
						$notParticipatedCards++;
					}
				}
			}
			
			$this->view->divinationsList = $this->divinationService->getOtherDivinationsInCategory($divination['category_id']);

			$this->view->haveNotParticipatedCards = $haveNotParticipatedCards;
			$this->view->notParticipatedCards = $notParticipatedCards;
			
			if($divtype != 'book' && $divtype != 'other'){
				$navItem1 = $this->view->navigation()->findOneById($divtype.'-'.$alias);

				if($navItem1){
					$navItem2 = $navItem1->findOneById($divalias);
					if($navItem2){
						$navItem2->setActive('true');
					}
				}
			}else{
				$navItem = $this->view->navigation()->findOneById($divalias);
				if($navItem){
					$navItem->setActive('true');
				}
			}
			
			$this->view->seotitle = 'Гадания::'.(App_DivinationService::getDivTypeRu($divtype)).'::'.$divination['name'];
			$this->view->seokeywords = $divination['seokeywords'];
			$this->view->seodescription = $divination['seodescription'];
			
			$this->view->attributes = array(
				'type' => 'divination',
				'subtype' => '',
				'sign' => '',
				'resource_id' => $divination['id'] 
			);
			$this->view->comments = $this->commentsService->getComments('divination', '', '', $divination['id']);
			//var_dump($this->view->divination); die;
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	public function getCardDescriptionAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$data = json_decode($this->_getParam('data',false));

		$error = false;
		if(is_array($data)) {
			if(isset($data[0]->divination_id) && is_numeric($data[0]->divination_id)){
				$positions = array();
				foreach($data as $item){
					if(isset($item->card_number) && is_numeric($item->card_number)){
						$positions[] = $item->card_number;
					}else{
						$error = true;
						break;
					}
				}
				if(!$error) {
					$divination = $this->divinationService->getDivinationById($data[0]->divination_id);
					$cardsData = $this->divinationService->getCardsByDivinationId($data[0]->divination_id);
					$divinationData = $this->divinationService->getDivinationDataItemByPositions($positions, $data[0]->divination_id);

					$matches = array();
					if ( in_array($divination['type'],array('classic','lenorman')) )
					{
						$matches = $this->divinationService->getMatchesByPositionsAndDivinationId($positions, $data[0]->divination_id);
					}

					//join with $divinationData with $cardsData
					foreach($divinationData as $index => $item)
					{
						//card position and description
						foreach($cardsData as $cardItem)
						{
							if( ($index + 1) == $cardItem['alignment_position']){
								$divinationData[$index]['alignment_position'] = $index+1;//$cardItem['alignment_position'];
								$divinationData[$index]['position_desc'] = $cardItem['position_desc'];
								break;
							}
						}
						//card side and deck
						foreach($data as $dataItem)
						{
							if($dataItem->card_number == $item['deck_position']){
								$divinationData[$index]['side'] = $dataItem->side;
								$divinationData[$index]['deck'] = $dataItem->deck;
							}
						}
						if ( in_array($divination['type'],array('classic','lenorman')) && $divination['matches'] == 'y')
						{
							//join with matches
							$divinationData[$index]['match'] = $matches[$index]['description'];
						}
					}
				}
			}else{
				$error = true;
			}
		}else{
			$error = true;
		}
		$json = array();
		if(!$error){
			$this->view->divinationData = $divinationData;
			$json['status'] = 'success';
			$json['response'] = $this->view->render('divination' . DIRECTORY_SEPARATOR . 'divination-description-items.phtml');
		}else{
			$json['status'] = 'fail';
			$json['response'] = '';
		}
		echo Zend_Json::encode($json);
	}
	public function getBookDescriptionAction(){
		$this->_helper->layout->disableLayout();
		$divId = $this->_getParam('divination_id',false);
		$hex = $this->_getParam('hexagramm',false);
		if($divId && $hex){
			$divination = $this->divinationService->getDivinationById($divId);
			$this->view->deck = App_UtilsService::generateTranslit($divination['name']);
			$this->view->item = $this->divinationService->getBookDescriptionItemByHex($divId,$hex);
			$this->render('divination-book-description-item');
		}
	}
	
	public function getOtherDescriptionAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$divId = $this->_getParam('divination_id',false);
		$order = $this->_getParam('order',false);
		if($divId && $order){
			$item = $this->divinationService->getOtherDescriptionItemByOrder($divId,$order);
			echo Zend_Json::encode($item);
		}
	}
	
	public function voteAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_getParam('id',false);
		if($id && $this->getRequest()->isPost()){
			if(!isset($_COOKIE['votediv_'.$id])){
				setcookie('votediv_'.$id, 'vote', time() + 3600*24*14, '/');
				$this->divinationService->setVote($id);
			}
		}
	} 
	
	public function postDispatch(){
		
	}
}