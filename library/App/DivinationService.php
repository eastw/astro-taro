<?php
class App_DivinationService {
	
	protected $divination;
	protected $divinationData;
	protected $divinationNet;
	protected $divinationDecks;
	protected $categoryService;
	protected $divinationBook;
	protected $bookAssosiateArray;
	protected $match;
	
	const TARO_CATEGORY = 1;
	const CLASSIC_CATEGORY = 2;
	const RUNE_CATEGORY = 3;
	const BOOK_CATEGORY = 4;
	const OTHER_CATEGORY = 5;
	const LENORMAN_CATEGORY = 5;
	
	public function __construct(){
		$this->divination = new Application_Model_DbTable_DivinationTable();
		$this->divinationData = new Application_Model_DbTable_DivinationDataTable();
		$this->divinationNet = new Application_Model_DbTable_DivinationNetTable();
		$this->divinationDecks = new Application_Model_DbTable_DivinationDecksTable();
		$this->divinationBook = new Application_Model_DbTable_DivinationBookTable();
		$this->match = new Application_Model_DbTable_MatchTable();
		$this->categoryService = App_CategoryService::getInstance();

		$this->bookAssosiateArray = array(
										'111111','000000','010001','100010','010111','111010','000010','010000','110111','111011',
										'000111','111000','111101','101111','000100','001000','011001','100110','000011','110000',
										'101001','100101','100000','000001','111001','100111','100001','011110','010010','101101',
										'011100','001110','111100','001111','101000','000101','110101','101011','010100','001010',
										'100011','110001','011111','111110','011000','000110','011010','010110','011101','101110',
										'001001','100100','110100','001011','001101','101100','110110','011011','110010','010011',
										'110011','001100','010101','101010'
		);
	}
	
	public function addDivination($data){
		$insertData = array(
			'name' => $data['title'],
			'activity' => 'n',
			'only_old_arkans' => $data['only_old_arkans'],
			'category_id' => $data['category'],
			'description' => $data['desc'],
			'raiting' => 0,
			'seokeywords' => $data['seokeywords'],
			'seodescription' => $data['seodescription'],
			'background' => $data['background'],
			'cards_in_alignment' => $data['cards'],
			'significators'	=> $data['significators'],
			'type_id' => $data['type'],
			'matches' => $data['matches']
		);
		
		if(!empty($data['image'])){
			$insertData['image'] = $data['image'];
		}
		if(!empty($data['alignment_form'])){
			$insertData['alignment_form'] = $data['alignment_form'];
		}
		if(!empty($data['background'])){
			$insertData['background'] = $data['background'];
		}
		if(!empty($data['front_background'])){
			$insertData['front_background'] = $data['front_background'];
		}
		
		if($data['category'] == self::BOOK_CATEGORY || $data['category'] == self::OTHER_CATEGORY){
			$insertData['cards_in_alignment'] = 1;
		}
		
		$id = $this->divination->insert($insertData);

		$updateData = array(
			'alias' => $id . '-' . App_UtilsService::generateTranslit($data['title']),
		);
		$this->divination->update($updateData, $this->divination->getAdapter()->quoteInto('id=?',$id));
		
		if(count($data['decks'])){
			foreach($data['decks'] as $index => $deck){
				$insertData = array(
					'deck_id' => $index,
					'divination_id' => $id
				);
				$this->divinationDecks->insert($insertData);
			}
		}
		$types = $this->categoryService->getCategoryTypes();
		foreach($types as $type){
			if($data['type'] == $type['id']){
				$data['type'] = $type['type'];
				break;
			}
		}

		$limit = 0;
		switch($data['type']){
			case 'taro': $limit = 78; break;
			case 'classic': $limit = 36; break;
			case 'lenorman': $limit = 36; break;
			case 'rune': $limit = 24; break;
			case 'book': $limit = 64; break;
			case 'other': $limit = 100; break;
		}

		if($data['type'] != 'book' && $data['type'] != 'other'){
			for($i = 0; $i < $limit; $i++ ){
				$insertData = array(
						'deck_position' => $i,
						'description' => ' ',
						'description_reverse' => ' ',
						'divination_id' => $id,
				);
				$this->divinationData->insert($insertData);
			}
			for($i = 0,$n = $data['cards'];$i <$n; $i++){
				$insertData = array(
						'net_column' => 0,
						'net_row' => 0,
						'alignment_position' => 0,
						'is_significator' => 'n',
						'divination_id' => $id,
						'position_desc' => ''
				);
				$this->divinationNet->insert($insertData);
			}
		}elseif($data['type'] == 'book'){
			$deckFolder = App_UtilsService::generateTranslit($data['title']);
			mkdir(realpath(dirname('.')).DIRECTORY_SEPARATOR.'files'.
					DIRECTORY_SEPARATOR.'decks'.DIRECTORY_SEPARATOR.$deckFolder);
			for($i = 0; $i < $limit; $i++ ){
				$insertData = array(
						'image' => '',
						'order' => ($i +1),
						'description' => '',
						'divination_id' => $id,
						'lines' => $this->bookAssosiateArray[$i]
				);
				$this->divinationBook->insert($insertData);
			}
		}elseif($data['type'] == 'other'){
			for($i = 0; $i < $limit; $i++ ){
				$insertData = array(
						'image' => '',
						'order' => ($i +1),
						'description' => '',
						'divination_id' => $id,
						'lines' => ''
				);
				$this->divinationBook->insert($insertData);
			}
		}
	}
	
	public function saveDivination($data, $id){

		$divination = $this->getDivinationById($id);

		$updateData = array(
			'name' => $data['title'],
			/*link not changed by customer desire*/
			/*'alias' => $id . '-' . App_UtilsService::generateTranslit($data['title']),*/
			'only_old_arkans' => $data['only_old_arkans'],
			'category_id' => $data['category'],
			'description' => $data['desc'],
			'seokeywords' => $data['seokeywords'],
			'seodescription' => $data['seodescription'],
			'cards_in_alignment' => $data['cards'],
			'significators'	=> $data['significators'],
			'type_id' => $data['type'],
			'matches' => $data['matches']
		);
		/* update images*/
		if(isset($data['background']) && !empty($data['background'])){
			$updateData['background'] = $data['background'];
		}
		if(isset($data['image']) && !empty($data['image'])){
			$updateData['image'] = $data['image'];
		}
		if(isset($data['alignment_form']) && !empty($data['alignment_form'])){
			$updateData['alignment_form'] = $data['alignment_form'];
		}
		if(isset($data['front_background']) && !empty($data['front_background'])){
			$updateData['front_background'] = $data['front_background'];
		}
		$this->divination->update($updateData, $this->divination->getAdapter()->quoteInto('id=?',$id));

		$cache = Zend_Registry::get('cache');
		$cache->remove(str_replace('.','_', $_SERVER['HTTP_HOST']) . '_' . $divination['type'] . '_list_data' );
		
		if( $divination['type'] == 'taro' || $divination['type'] == 'classic' || $divination['type'] == 'rune'
			|| $divination['type'] == 'lenorman' ){
			/*renew divination-decks links*/
			$this->divinationDecks->delete($this->divination->getAdapter()->quoteInto('divination_id=?',$id));
			foreach($data['decks'] as $index => $deck){
				$insertData = array(
					'divination_id' => $id,
					'deck_id' => $index
				);
				$this->divinationDecks->insert($insertData);
			}
		
			//remove card descriptions if type is changed
			if($divination['type_id'] != $data['type']){
				$this->divinationData->delete($this->divinationData->getAdapter()->quoteInto('divination_id=?',$id));
				$categoryService = App_CategoryService::getInstance();
				$types = $categoryService->getCategoryTypes();
				foreach($types as $type){
					if($data['type'] == $type['id']){
						$data['type'] = $type['type'];
						break;
					}
				}
				$limit = 0;
				switch($data['type']){
					case 'taro': $limit = 78; break;
					case 'classic': $limit = 36; break;
					case 'lenorman': $limit = 36; break;
					case 'rune': $limit = 24; break;
				}
				for($i = 0; $i < $limit; $i++ ){
					$insertData = array(
							'deck_position' => $i,
							'description' => ' ',
							'description_reverse' => ' ',
							'divination_id' => $id,
					);
					$this->divinationData->insert($insertData);
				}
			}
			$cards = $this->divinationNet->fetchAll($this->divinationNet->getAdapter()->quoteInto('divination_id=?', $id))->toArray();
			if(count($cards) > $data['cards']){
				$must_delete = array();
				foreach($cards as $index => $card){
					if( ($index + 1) > $data['cards']){
						$must_delete[] = $card;
					}
				}
				if(count($must_delete)){
					foreach ($must_delete as $item){
						$this->divinationNet->delete($this->divinationNet->getAdapter()->quoteInto('id=?', $item['id']));
					}
				}
			}else{
				$resultCount = $data['cards'] - count($cards);
				for($i = 0; $i < $resultCount;$i++){
					$insertData = array(
							'net_column' => 0,
							'net_row' => 0,
							'alignment_position' => 0,
							'is_significator' => 'n',
							'divination_id' => $id,
							'position_desc' => ''
					);
					$this->divinationNet->insert($insertData);
				} 
			}
		}
	}
	
	public function listDivinationsQuery(){
		$query = $this->divination->getAdapter()->select();
		$query->from(array('d'=>'divination'))
			->joinLeft(array('c' => 'category_types'), 'd.type_id = c.id',array('type','type_name' => 'name'))
			->joinLeft(array('cat' => 'category'), 'd.category_id = cat.id',array('cat_name' => 'name'))
		 ->order('id DESC');
		return $query;
	}
	
	public function getDivinationById($id){
		$query = $this->divination->getAdapter()->select();
		$query->from(array('d'=>'divination'))
			->joinLeft(array('c' => 'category_types'), 'd.type_id = c.id','type')
			->where($this->divination->getAdapter()->quoteInto('d.id=?',$id));
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetch();
	}
	
	public function deleteDivination($id){
		$divination = $this->getDivinationById($id);
		$cache = Zend_Registry::get('cache');
		$cache->remove(str_replace('.','_', $_SERVER['HTTP_HOST']) . '_' . $divination['type'] . '_list_data' );
		$realPath = realpath(dirname('.')) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'divinations' . DIRECTORY_SEPARATOR . $divination['background'];
		if(file_exists($realPath)){
			unlink($realPath);
		}
		$realPath = realpath(dirname('.')) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'divinations' . DIRECTORY_SEPARATOR . $divination['image'];
		if(file_exists($realPath)){
			unlink($realPath);
		}
		$realPath = realpath(dirname('.')) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'divinations' . DIRECTORY_SEPARATOR . $divination['alignment_form'];
		if(file_exists($realPath)){
			unlink($realPath);
		}
		$realPath = realpath(dirname('.')) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'divinations' . DIRECTORY_SEPARATOR . $divination['front_background'];
		if(file_exists($realPath)){
			unlink($realPath);
		}
		$this->divinationDecks->delete($this->divinationDecks->getAdapter()->quoteInto('divination_id=?',$id));
		$this->divination->delete($this->divination->getAdapter()->quoteInto('id=?',$id));
		$this->divinationData->delete($this->divinationData->getAdapter()->quoteInto('divination_id=?',$id));
		$this->divinationBook->delete($this->divinationBook->getAdapter()->quoteInto('divination_id=?',$id));
	}
	
	public function divinationDataIds($id){
		$query = $this->divination->getAdapter()->select();
		$query->from('divination_data',array('id','deck_position'))
				->where($this->divination->getAdapter()->quoteInto('divination_id=?',$id))
				->order('id');
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function getDivinationDataById($dataid){
		return $this->divinationData->fetchRow($this->divinationData->getAdapter()->quoteInto('id=?',$dataid))->toArray();
	}
	public function saveDivinationDataItem($data){
		$updateData = array(
			'title' => $data['title'],
			'title_reverse' => $data['title_reverse'],
			'description' => $data['description'],
			'description_reverse' => $data['description_reverse'],
		);
		$this->divinationData->update($updateData, $this->divinationData->getAdapter()->quoteInto('id=?',$data['id']));
	}
	
	public function getCardsByDivinationId($id){
		$query = $this->divinationNet->getAdapter()->select();
		$query->from('divination_net')
			->where($this->divination->getAdapter()->quoteInto('divination_id=?',$id))
			->order('alignment_position ASC');
		$stm = $query->query();
		return $stm->fetchAll();
	}

	public function updateDivinationNetItemById($data,$id){
		$updateData = array(
			'net_column' => $data['column'],
			'net_row' => $data['row'],
			'alignment_position' => $data['order'],
			'position_desc' => $data['position_desc'],
			'participation' => $data['participation']
		);
		if($data['is_significator'] != 0){
			$updateData['is_significator'] = 'y';
			$updateData['significator_type'] = $data['significator_type'];
			$updateData['participation'] = $data['participation'];
			if($data['participation'] == 'n'){
				$updateData['alignment_position'] = -1;
			}
		}else{
			$updateData['is_significator'] = 'n';
		}
		$this->divinationNet->update($updateData, $this->divinationNet->getAdapter()->quoteInto('id=?',$id));
		return $this->divinationNet->fetchRow($this->divinationNet->getAdapter()->quoteInto('id=?',$id))->toArray();
	}
	
	public function getDivinationDataItemByPosition($deckPosition,$divId){
		$query = $this->divination->getAdapter()->select();
		$query->from('divination_data')
			->where($this->divination->getAdapter()->quoteInto('divination_id=?',$divId))
			->where($this->divination->getAdapter()->quoteInto('deck_position=?',$deckPosition));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetch();
	}

	public function getDivinationDataItemByPositions($positions, $divId){
		$query = $this->divination->getAdapter()->select();
		$query->from('divination_data')
			->where($this->divination->getAdapter()->quoteInto('deck_position IN (?)', $positions ))
			->where($this->divination->getAdapter()->quoteInto('divination_id=?',$divId))
			->order($this->divination->getAdapter()->quoteInto("FIELD(deck_position,?)",$positions));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function resetDivinationNet($id){
		$updateData = array(
				'net_column' => 0,
				'net_row' => 0,
				'alignment_position' => 0,
				'position_desc' => '',
				'is_significator' => 'n',
				'significator_type' => '',
				'participation' => 'y',
		);
		$this->divinationNet->update($updateData, $this->divinationNet->getAdapter()->quoteInto('divination_id=?',$id));
	}
	
	public function changeDivinationActivity($id){
		$divination = $this->getDivinationById($id);
		
		if($divination['activity'] == 'y'){
			$updateData['activity'] = 'n';
		}else{
			$updateData['activity'] = 'y';
		}
		$this->divination->update($updateData, $this->divination->getAdapter()->quoteInto('id=?',$id));
		$cache = Zend_Registry::get('cache');
		$cache->remove(str_replace('.','_', $_SERVER['HTTP_HOST']) . '_' . $divination['type'] . '_list_data');
		$result = array('errors' => array());
		return $result;
	}
	
	public function getListDivinationsWithCategories($divtype,$divId){
		$cache = Zend_Registry::get('cache');
		if(!$data = $cache->load(str_replace('.','_', $_SERVER['HTTP_HOST']) . '_'  . $divtype . '_list_data')){
			switch($divtype){
				case 'taro': 
					$data = $this->getTaroData($divtype,$divId);
					break;
				case 'classic':
					$data = $this->getClassicData($divtype,$divId);
					break;
				case 'lenorman':
					$data = $this->getLenormanData($divtype,$divId);
					break;
				case 'rune':
					$data = $this->getRuneData($divtype,$divId);
					break;
				case 'book':
					$data = $this->getBookData($divtype,$divId);
					break;
				case 'other':
					$data = $this->getOtherData($divtype,$divId);
					break;
			}
			$cache->save($data, str_replace('.','_', $_SERVER['HTTP_HOST']) . '_'  . $divtype . '_list_data');
		}
		return $data;
	}
	
	protected function getTaroData($divtype, $divId){
		$categories = $this->categoryService->getChildCategoriesByType($divId);
		$cat_ids = array();
		foreach ($categories as $category){
			$cat_ids[] = $category['id'];
		}
		$cat_str = implode(',', $cat_ids);
		$query = $this->divination->select();
		$query->from('divination')->where('category_id in ('.$cat_str.') AND activity=\'y\'')->order('id DESC');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$divinations = $stm->fetchAll();
		
		foreach($categories as $catindex => &$category){
			$children = array();
			foreach($divinations as $div){
				if($div['category_id'] == $category['id']){
					$children[] = $div;
				}
			}
			$category['children'] = $children;
			$category['children']['vis'] = array();
			$category['children']['unvis'] = array();
			foreach($children as $index => $child){
				if($index < 3){
					$categories[$catindex]['children']['vis'][] = $child;
				}else{
					$categories[$catindex]['children']['unvis'][] = $child;
				}
			}
		}
		$categories['root-category'] = $this->categoryService->getCategory($categories[0]['parent_id'])->toArray();
		return $categories;
	}
	
	protected function getClassicData($divtype,$divId){
		$categories = $this->categoryService->getChildCategoriesByType($divId);
		
		$cat_ids = array();
		foreach ($categories as $category){
			$cat_ids[] = $category['id'];
		}
		$cat_str = implode(',', $cat_ids);
		$query = $this->divination->select();
		$query->from('divination')->where('category_id in (' . $cat_str . ') AND activity=\'y\'')->order('id DESC');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$divinations = $stm->fetchAll();

		foreach($categories as $catindex => &$category){
			$children = array();
			foreach($divinations as $div){
				if($div['category_id'] == $category['id']){
					$children[] = $div;
				}
			}
			$category['children'] = $children;
			$category['children']['vis'] = array();
			$category['children']['unvis'] = array();
			foreach($children as $index => $child){
				if($index < 3){
					$categories[$catindex]['children']['vis'][] = $child;
				}else{
					$categories[$catindex]['children']['unvis'][] = $child;
				}
			}
		}
		$categories['root-category'] = $this->categoryService->getCategory($categories[0]['parent_id'])->toArray(); 
		return $categories;
	}

	protected function getLenormanData($divtype,$divId){
		$categories = $this->categoryService->getChildCategoriesByType($divId);

		$cat_ids = array();
		foreach ($categories as $category){
			$cat_ids[] = $category['id'];
		}
		$query = $this->divination->select();
		$query->from('divination')->where($this->divination->getAdapter()->quoteInto('category_id IN (?) AND activity=\'y\' ',$cat_ids))
			->order('id DESC');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$divinations = $stm->fetchAll();

		foreach($categories as $catindex => &$category){
			$children = array();
			foreach($divinations as $div){
				if($div['category_id'] == $category['id']){
					$children[] = $div;
				}
			}
			$category['children'] = $children;
			$category['children']['vis'] = array();
			$category['children']['unvis'] = array();
			foreach($children as $index => $child){
				if($index < 3){
					$categories[$catindex]['children']['vis'][] = $child;
				}else{
					$categories[$catindex]['children']['unvis'][] = $child;
				}
			}
		}
		$categories['root-category'] = $this->categoryService->getCategory($categories[0]['parent_id'])->toArray();
		return $categories;
	}
	
	protected function getRuneData($divtype, $divId){
		$categories = $this->categoryService->getChildCategoriesByType($divId);
		$cat_ids = array();
		foreach ($categories as $category){
			$cat_ids[] = $category['id'];
		}
		$cat_str = implode(',', $cat_ids);
		$query = $this->divination->select();
		$query->from('divination')->where('category_id in ('.$cat_str.') AND activity=\'y\' ')->order('id DESC');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$divinations = $stm->fetchAll();

		foreach($categories as $catindex => &$category){
			$children = array();
			foreach($divinations as $div){
				if($div['category_id'] == $category['id']){
					$children[] = $div;
				}
			}
			$category['children'] = $children;
			$category['children']['vis'] = array();
			$category['children']['unvis'] = array();
			foreach($children as $index => $child){
				if($index < 3){
					$categories[$catindex]['children']['vis'][] = $child;
				}else{
					$categories[$catindex]['children']['unvis'][] = $child;
				}
			}
		}
		$categories['root-category'] = $this->categoryService->getCategory($categories[0]['parent_id'])->toArray();
		return $categories;
	}
	
	protected function getBookData($divtype, $divId){
		$categories = $this->categoryService->getRootCategoriesByType($divId);
		
		$query = $this->divination->select();
		$query->from('divination')->where('type_id = ('.$divId.') AND activity=\'y\' ')->order('id DESC');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$divinations = $stm->fetchAll();

		foreach($categories as &$category){
			$children = array();
			foreach($divinations as $div){
				if($div['category_id'] == $category['id']){
					$children[] = $div;
				}
			}
			$category['children'] = $children;
		}
		return $categories[0];
	}
	
	protected function getOtherData($divtype,$divId){
		$categories = $this->categoryService->getRootCategoriesByType($divId);
		
		$query = $this->divination->select();
		$query->from('divination')->where('type_id = ('.$divId.') AND activity=\'y\' ')->order('id DESC');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$divinations = $stm->fetchAll();

		foreach($categories as &$category){
			$children = array();
			foreach($divinations as $div){
				if($div['category_id'] == $category['id']){
					$children[] = $div;
				}
			}
			$category['children'] = $children;
		}
		return $categories[0];
	}
	
	public function getDivinationByAlias($alias){
		$aliasArray = explode('-',$alias);
		if(count($aliasArray) >= 2 ){
			$data = $this->getDivinationById($aliasArray[0]);
			if($data && $data['alias'] == $alias){
				return $data;
			}else{
				throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
			}
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
	}
	
	public function getBookDescriptionsByDivinationId($id){
		return $this->divinationBook->fetchAll($this->divinationBook->getAdapter()->quoteInto('divination_id=?',$id))->toArray();
	}
	
	public function getBookDescriptionItem($divId,$order){
		$query = $this->divinationBook->select();
		$query->from(array('d' => 'divination_book'))
			->where($this->divinationBook->getAdapter()->quoteInto('d.divination_id =?',$divId))
			->where($this->divinationBook->getAdapter()->quoteInto('d.order=?',$order));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetch();
	}
	public function getBookDescriptionItemByHex($divId,$hex){
		$query = $this->divinationBook->select();
		$query->from(array('d' => 'divination_book'))
		->where($this->divinationBook->getAdapter()->quoteInto('d.divination_id =?',$divId))
		->where($this->divinationBook->getAdapter()->quoteInto('d.lines=?',$hex));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetch();
	}

	public function getBookDescriptionItemByOrder($divId,$order){
		$query = $this->divinationBook->select();
		$query->from(array('d' => 'divination_book'))
			->where($this->divinationBook->getAdapter()->quoteInto('d.divination_id =?',$divId))
			->where($this->divinationBook->getAdapter()->quoteInto('d.order=?',$order));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetch();
	}
	
	public function getOtherDescriptionItemByOrder($divId,$order){
		$query = $this->divinationBook->select();
		$order = mt_rand(1,100);
		$query->from(array('d' => 'divination_book'))
			->where($this->divinationBook->getAdapter()->quoteInto('d.divination_id =?',$divId))
			->where($this->divinationBook->getAdapter()->quoteInto('d.order=?',$order));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$data = $stm->fetch();
		unset($data['id']);
		return $data;
	}
	
	public function updateBookDescriptionItem($item){
		$this->divinationBook->update($item, $this->divinationBook->getAdapter()->quoteInto('id=?',$item['id']));
	}
	
	public function setVote($id){
		$updateData = array(
			'raiting' => new Zend_Db_Expr('raiting + 1')
		);
		$this->divination->update($updateData,$this->divination->getAdapter()->quoteInto('id=?', $id));
		
	}
	
	public function getDivinationsByCategory($categoryId){
		return $this->divination->fetchAll($this->divination->getAdapter()->quoteInto('category_id=?', $categoryId))->toArray();
	}
	
	public function copyDivinationDescriptions($fromDivId,$toDivId){
		$result = array('result' => 'success','errors' => array());
		$fromDiv = $this->getDivinationById($fromDivId);
		$toDiv = $this->getDivinationById($toDivId);
		if($fromDiv['type_id'] != $toDiv['type_id']){
			$result['result'] = 'fail';
			$result['errors'][] = 'Не совпадают типы гадания';
			return $result;
		}
		$fromQuery = $this->divinationData->select();
		$fromQuery->from('divination_data')->where($this->divinationData->getAdapter()->quoteInto('divination_id=?', $fromDivId))->order('deck_position');
		$stm = $fromQuery->query(Zend_Db::FETCH_ASSOC);
		$fromData = $stm->fetchAll();
		
		$toQuery = $this->divinationData->select();
		$toQuery->from('divination_data')->where($this->divinationData->getAdapter()->quoteInto('divination_id=?', $toDivId))->order('deck_position');
		$stm = $toQuery->query(Zend_Db::FETCH_ASSOC);
		$toData = $stm->fetchAll();
		
		if(count($fromData) != count($toData)){
			$result['result'] = 'fail';
			$result['errors'][] = 'У гаданий разное количество описаний ';
			return $result;
		}
		foreach($toData as $index => $toItem){
			$updateData = array(
				'description' => $fromData[$index]['description'],
			 	'description_reverse' => $fromData[$index]['description_reverse'],
				'title' => $fromData[$index]['title'],
				'title_reverse' => $fromData[$index]['title_reverse'],
			);
			$this->divinationData->update($updateData, $this->divinationData->getAdapter()->quoteInto('id=?', $toItem['id']));
		}
		return $result;
	}

	public function copyDivinationMatches($fromDivId,$toDivId){
		$result = array('result' => 'success','errors' => array());
		$fromDiv = $this->getDivinationById($fromDivId);
		$toDiv = $this->getDivinationById($toDivId);
		if($fromDiv['type_id'] != $toDiv['type_id']){
			$result['result'] = 'fail';
			$result['errors'][] = 'Не совпадают типы гадания';
			return $result;
		}
		$fromQuery = $this->match->select();
		$fromQuery->from('divination_match')
			->setIntegrityCheck(false)
			->where($this->match->getAdapter()->quoteInto('divination_id=?', $fromDivId));
		$stm = $fromQuery->query(Zend_Db::FETCH_ASSOC);
		$fromData = $stm->fetchAll();

		$this->match->delete($this->match->getAdapter()->quoteInto('id=?', $toDivId));

		foreach($fromData as $index => $fromItem){
			$insertData = array(
				'card_num' => $fromItem['card_num'],
				'next_card_num' => $fromItem['next_card_num'],
				'description' => $fromItem['description'],
				'divination_id' => $toDivId
			);
			$this->match->insert($insertData);
		}
		return $result;
	}
	
	public function getDivinationByMask($mask){
		$query =$this->divination->getAdapter()->select();
		$query->from(array('d' => 'divination'),array('id','title'=>'name'))
			->joinLeft(array('c' => 'category'), 'd.category_id = c.id',array('category_name'=>'name'))
			->joinLeft(array('t' => 'category_types'), 'd.type_id = t.id','name')
			->where('d.name LIKE ?','%'.$mask.'%');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$result = $stm->fetchAll();
		foreach($result as $index => $item){
			$result[$index]['title'] = $item['name'].' \ '.$item['category_name'].' \ '.$item['title'];
		}
		return $result; 
	}
	
	public static function getDivTypeRu($divType){
		switch($divType){
			case 'taro': return 'Таро';
			case 'classic': return 'Классические карты';
			case 'rune': return 'Руны';
			case 'book': return 'Книга перемен';
			case 'other': return 'Другие гадания';
			case 'lenorman': return 'Гадания мадам Ленорман';
		}
	}
	
	public function searchDivination($squery){
		$query =$this->divination->getAdapter()->select();
		if(!empty($squery)){
			$query->from(array('d'=>'divination'))
			->joinLeft(array('c' => 'category_types'), 'd.type_id = c.id',array('type','type_name' => 'name'))
			->joinLeft(array('cat' => 'category'), 'd.category_id = cat.id',array('cat_name' => 'name'))
			->where('d.name LIKE \'%'.$squery.'%\' ');
			
		}else{
			$query->from('divination');
		}
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function getOtherDivinationsInCategory($categoryId){
		$query =$this->divination->getAdapter()->select();
		$query->from(array('d'=>'divination'))
			->joinLeft(array('c' => 'category_types'), 'd.type_id = c.id','type')
			->joinLeft(array('cc' => 'category'), 'd.category_id = cc.id',array('category_alias' => 'alias'))
			->where($this->divination->getAdapter()->quoteInto('d.category_id=?',$categoryId))
			->where('d.activity = "y"')->order('id DESC');
		$stm = $query->query();
		return $stm->fetchAll();
	}

	public function getMatchByCardsAndDivinationId($card, $nextCard, $divinationId){
		$query =$this->match->getAdapter()->select();
		$query->from(array('d'=>'divination_match'))
			->where($this->divination->getAdapter()->quoteInto('card_num=?',$card))
			->where($this->divination->getAdapter()->quoteInto('next_card_num=?',$nextCard))
			->where($this->divination->getAdapter()->quoteInto('divination_id=?',$divinationId));
		$stm = $query->query();
		return $stm->fetch();
	}

	public function saveMatch($data){
		$match = $this->getMatchByCardsAndDivinationId($data['card_num'], $data['next_card_num'], $data['divination_id']);
		if($match){
			$updateData = array(
				'description' => $data['description']
			);
			$this->match->update($updateData, $this->match->getAdapter()->quoteInto('id=?',$match['id']) );
		}else{
			$insertData = array(
				'card_num' => $data['card_num'],
				'next_card_num' => $data['next_card_num'],
				'description' => $data['description'],
				'divination_id' => $data['divination_id'],
			);
			$this->match->insert($insertData);
		}
	}

	public function getMatchesByPositionsAndDivinationId($positions, $divinationId){
		$query =$this->match->getAdapter()->select();
		$query->from(array('d'=>'divination_match'))
			->where($this->divination->getAdapter()->quoteInto('card_num in (?)',$positions))
			->where($this->divination->getAdapter()->quoteInto('divination_id=?',$divinationId));
		$stm = $query->query();
		$rawMatches = $stm->fetchAll();

		$cleanMatches = array();

		$positionsCount = count($positions);
		foreach($positions as $index => $position){
			if($index != $positionsCount -1){
				foreach($rawMatches as $item){
					if($item['card_num'] == $position && $item['next_card_num'] == $positions[$index + 1]){
						$cleanMatches[$index] = array(
							'description' => $item['description']
						);
					}
				}
				if(!isset($cleanMatches[$index])){
					$cleanMatches[$index] = array('description' => '');
				}
			}else{
				$cleanMatches[] = array('description' => '');
			}
		}
		return $cleanMatches;
	}

	public function fillMatches($divinationId){
		for($i = 0; $i < 36; $i++){
			for($j = 0; $j < 36; $j++){
				$insertData = array(
					'card_num' => $i,
					'next_card_num' => $j,
					'description' => $i . '=>' . $j,
					'divination_id' => $divinationId
				);
				$this->match->insert($insertData);
			}
		}
	}
}
