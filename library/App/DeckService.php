<?php
class App_DeckService {
	
	protected $deck;
	protected $divinationDecks;
	
	public function __construct(){
		$this->deck = new Application_Model_DbTable_DeckTable();
		$this->divinationDecks = new Application_Model_DbTable_DivinationDecksTable();
	}
	
	public function listDecksQuery(){
		$query = $this->deck->getAdapter()->select();
		return $query->from('deck')->order('id DESC');
	}
	
	public function listDecks($type){
		$query =$this->deck->getAdapter()->select();
		$query->from(array('d'=>'deck'))
			->joinLeft(array('c' => 'category_types'), 'd.type_id = c.id','type')
			->where($this->deck->getAdapter()->quoteInto('d.activity="y" AND c.type=?',$type));
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function addDeck($data){
		$folder = App_UtilsService::generateTranslit($data['title']);
		$insertData = array(
				'name' => $data['title'],
				'folder_alias' => $folder,
				'activity' => 'n',
				'back' => $data['back'],
				'reshuffle' => $data['reshuffle'],
				'type_id' => $data['type'],
		);
		$this->deck->insert($insertData);
	}
	
	public function saveDeck($data,$id){
		$folder = App_UtilsService::generateTranslit($data['title']);
		$updateData = array(
				'name' => $data['title'],
				'folder_alias' => $folder,
				'type_id' => $data['type'],
		);
		
		if(isset($data['back'])){
			$updateData['back'] = $data['back'];
		}
		if(isset($data['reshuffle'])){
			$updateData['reshuffle'] = $data['reshuffle'];
		}
		$this->deck->update($updateData, $this->deck->getAdapter()->quoteInto('id=?',$id));
	}
	public function changeDeckActivity($id){
		$deck = $this->getDeckById($id);
		$path = realpath(dirname('.')) . DIRECTORY_SEPARATOR .
								'files' . DIRECTORY_SEPARATOR . 'decks' . DIRECTORY_SEPARATOR.
								$deck['folder_alias'] . DIRECTORY_SEPARATOR;
		$files = scandir($path);
		$absent = true;
		$filesCount = 0;
		$filesLimit = 0;
		$categoryService = App_CategoryService::getInstance();
		$types = $categoryService->getCategoryTypes();
		foreach ($types as $type){
			if($type['id'] == $deck['type_id']){
				$deck['type'] = $type['type'];
			}
		}
		switch($deck['type']){
			case 'taro': $filesLimit = 78; break;
			//in classic cards not all cards have reverse card
			case 'classic': $filesLimit = 36; break;
			case 'lenorman': $filesLimit = 36; break;
			case 'rune': $filesLimit = 24; break;
		}
		$foundFiles = array();
		for($i = 0; $i < $filesLimit; $i++){
			if(in_array($i . '.jpg', $files)){
				$absent = false;
			}
			if(in_array($i . '.png', $files)){
				$absent = false;
				$foundFiles[] = $i . '.png';
			}
			if(in_array($i . '.gif', $files)){
				$absent = false;
			}
			if(!$absent){
				++$filesCount;
			}
			$absent = true;
			if(in_array($i . '_0.jpg', $files)){
				$absent = false;
			}
			if(in_array($i . '_0.png', $files)){
				$absent = false;
				$foundFiles[] = $i . '_0.png';
			}
			if(in_array($i . '_0.gif', $files)){
				$absent = false;
			}
			if(!$absent){
				++$filesCount;
			}
			$absent = true;
		}
		$result = array();
		$result['id'] = $deck['id'];

		switch($deck['type']){
			case 'taro': $filesLimit *= 2; break;
			//in classic cards not all cards have reverse card
			case 'classic': $filesLimit = 48; break;
			case 'lenorman': $filesLimit = 36; break;
			case 'rune': $filesLimit *= 2; break;
		}
		if($filesCount != $filesLimit && $deck['activity'] == 'n'){
			$result['error'] = 'Не для всех карт заданы изображения';
		}else{
			$result['error'] = '';
		}
		if(empty($result['error'])){
			$updateData = array(
				'activity' => 'y'
			);
			if($deck['activity'] == 'y'){
				$updateData['activity']= 'n';
			}
			$this->deck->update($updateData, $this->deck->getAdapter()->quoteInto('id=?', $id));
			$result['activity'] = $updateData['activity'];
		}
		return $result;
	}
	
	public function removeDeck($id){
		$deck = $this->getDeckById($id);
		$path = realpath(dirname('.')).DIRECTORY_SEPARATOR.
									'files'.DIRECTORY_SEPARATOR.'decks'.DIRECTORY_SEPARATOR.
									$deck['folder_alias'].DIRECTORY_SEPARATOR;
		if(file_exists($deck['back'])){
			unlink($path.$deck['back']);
		}
		if(file_exists($deck['reshuffle'])){
			unlink($path.$deck['reshuffle']);
		}
		if ($objs = glob($path."*")) {
			foreach($objs as $obj) {
				unlink($obj);
			}
		}
		rmdir($path);
		$this->divinationDecks->delete($this->divinationDecks->getAdapter()->quoteInto('deck_id=?',$id));
		$this->deck->delete($this->deck->getAdapter()->quoteInto('id=?',$id));
	}
	
	public function getDeckById($id){
		$query = $this->deck->getAdapter()->select();
		$query->from(array('d'=>'deck'))
		->joinLeft(array('c' => 'category_types'), 'd.type_id = c.id','type')
		->where($this->deck->getAdapter()->quoteInto('d.id=?',$id));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$result = $stm->fetchAll();
		return $result[0];
	}
	
	public function getDecksByDivination($id){
		$query = $this->deck->getAdapter()->select();
		$subquery = $this->deck->getAdapter()->select();
		$subquery->from('divination_decks',array('deck_id'))->where($this->deck->getAdapter()->quoteInto('divination_id=?', $id));
		
		$query->from(array('d' => 'deck'),
				array('id','name','folder_alias','back','reshuffle'))
				->where('d.activity = "y"')
				->where('d.id IN ?',$subquery);
				
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function getDecksByType($typeId){
		$query = $this->deck->getAdapter()->select();
		$query->from('deck')->where($this->deck->getAdapter()->quoteInto('type_id=?', $typeId))
			->where("activity = 'y' ");
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	public function searchDeck($squery){
		$query =$this->deck->getAdapter()->select();
		if(!empty($squery)){
			$query->from(array('d'=>'deck'))
			->where('d.name LIKE \'%'.$squery.'%\' ');
		}else{
			$query->from('deck');
		}
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
}