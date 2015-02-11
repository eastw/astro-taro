<?php
require_once(APPLICATION_PATH . '/../library/Sphinx/api/sphinxapi.php');
class App_SearchService {
	
	protected $sphinx;
	
	const ARTICLE_TYPE = 1;
	const NEWS_TYPE = 2;
	const MAGIC_TYPE = 3;
	const DIVINATION_TYPE = 4;
	
	protected $article;
	protected $divination;
	
	protected $tagService;
	
	protected $itemsPerPage = 15;
	protected $startResult;
	protected $curPage;
	protected $pageArray = array();
	protected $indexes;
	
	public function __construct(){
		$this->sphinx = new SphinxClient();
		$this->sphinx->SetServer('127.0.0.1', 9312);
		
		//$this->sphinx->SetMatchMode(SPH_MATCH_EXTENDED2);
		$this->sphinx->SetMatchMode(SPH_MATCH_ANY);
		$this->sphinx->SetSortMode(SPH_SORT_RELEVANCE);
		//$this->sphinx->SetArrayResult(true);
		
		$this->article = new Application_Model_DbTable_ArticleTable();
		$this->divination = new Application_Model_DbTable_DivinationTable();
		
		$this->tagService = App_TagService::getInstance();

		$host = str_replace('.','_', $_SERVER['HTTP_HOST']);
		$this->indexes = array(
			$host . '_article_index',
			$host . '_news_index',
			$host . '_magic_index',
			$host . '_divination_index',
			$host . '_dream_word_index',
		);
	}
	
	protected function prepareData($page){
		if(is_numeric($page)){
			if($page < 0){
				$this->curPage = 1;
			}else{
				$this->curPage = $page;
			}
		}else{
			$this->curPage = 1;
		}
		$this->startResult = ($this->itemsPerPage * $this->curPage) - $this->itemsPerPage;
	}
	
	public function search($query,$page){
		
		$this->prepareData($page);
		
		$this->sphinx->SetLimits($this->startResult, $this->itemsPerPage);
		
		$tokens = explode(' ',$query);
		foreach($tokens as $index => $token){
			$tokens[$index] = "(".$token." | *".$token."*)";
		}
		$query = implode(' ', $tokens);
		
		$rawData = $this->sphinx->Query($query, implode(' ', $this->indexes));
		
		$result = array('items' => array(),'total' => $rawData['total_found']);
		
		if($rawData && $rawData['total_found'] > 0){
			if(isset($rawData['matches'])){
				$ids = array_keys($rawData['matches']);
				$articleIds = array();
				$newsIds = array();
				$magicIds = array();
				$divinationIds = array();
				
				if(count($ids)){
					foreach($ids as $id){
						switch($rawData['matches'][$id]['attrs']['source_id']){
							case self::ARTICLE_TYPE: $articleIds[] = $id; break;
							case self::NEWS_TYPE: $newsIds[] = $id; break;
							case self::MAGIC_TYPE: $magicIds[] = $id; break;
							case self::DIVINATION_TYPE: $divinationIds[] = $id; break;
						}
					}
				}
				$articleData = array();
				$newsData = array();
				$magicData = array();
				$divinationData = array();
				
				$adapter = $this->article->getAdapter();
				if(count($articleIds)){
					$query = $this->article->select();
					$query->from('article')->where($adapter->quoteInto('id in (?)',$articleIds));
					$stm = $query->query(Zend_Db::FETCH_ASSOC);
					$articleData = $stm->fetchAll();
					foreach($articleData as $index => $article){
						$ids_array = explode(';',$article['quicktag']);
						if(isset($ids_array[0]) && !empty($ids_array[0])){
							$articleData[$index]['tag_id'] = $ids_array[0]; 
						}else{
							// no tag id
							$articleData[$index]['tag_id'] = 17;
						}
					}
					$tags = $this->tagService->getTags();
					foreach($articleData as $index => $article){
						foreach($tags as $tag){
							if($article['tag_id'] == $tag['id']){
								$articleData[$index]['tag_data'] = $tag;
							}
						}
					}
				}
				if(count($newsIds)){
					$query = $this->article->select();
					$query->from('article')->where($adapter->quoteInto('id in (?)',$newsIds));
					$stm = $query->query(Zend_Db::FETCH_ASSOC);
					$newsData = $stm->fetchAll();
					foreach($newsData as $index => $news){
						$ids_array = explode(';',$news['quicktag']);
						if(isset($ids_array[0]) && !empty($ids_array[0])){
							$newsData[$index]['tag_id'] = $ids_array[0];
						}else{
							// no tag id
							$newsData[$index]['tag_id'] = 17;
						}
					}
					$tags = $this->tagService->getNewsTags();
					foreach($newsData as $index => $news){
						foreach($tags as $tag){
							if($news['tag_id'] == $tag['id']){
								$newsData[$index]['tag_data'] = $tag;
							}
						}
					}
				}
				if(count($magicIds)){
					$query = $this->article->select();
					$query->from('article')->where($adapter->quoteInto('id in (?)',$magicIds));
					$stm = $query->query(Zend_Db::FETCH_ASSOC);
					$magicData = $stm->fetchAll();
					
					foreach($magicData as $index => $magic){
						$ids_array = explode(';',$magic['quicktag']);
						if(isset($ids_array[0]) && !empty($ids_array[0])){
							$magicData[$index]['tag_id'] = $ids_array[0];
						}else{
							// no tag id
							$magicData[$index]['tag_id'] = 17;
						}
					}
					$tags = $this->tagService->getMagicTags();
					foreach($magicData as $index => $magic){
						foreach($tags as $tag){
							if($magic['tag_id'] == $tag['id']){
								$magicData[$index]['tag_data'] = $tag;
							}
						}
					}
				}
				if(count($divinationIds)){
					$query = $this->divination->select();
					$query->setIntegrityCheck(false);
					$query->from(array('d' =>'divination'))
						->joinLeft(array('c' => 'category'), 'c.id = d.category_id',array('category_alias'=>'alias'))
						->joinLeft(array('ct' => 'category_types'), 'ct.id = d.type_id',array('type'))
						->where($adapter->quoteInto('d.id in (?)',$divinationIds));
					$stm = $query->query(Zend_Db::FETCH_ASSOC);
					$divinationData = $stm->fetchAll();
				}
				foreach($rawData['matches'] as $id => $item){
					$seacrhItem = array();
					if($item['attrs']['source_id'] == self::ARTICLE_TYPE){
						foreach($articleData as $article){
							if($article['id'] == $id){
								$seacrhItem = $article;
								break;
							}
						}
						$seacrhItem['startlink'] = 'statyi';
					}
					if($item['attrs']['source_id'] == self::NEWS_TYPE){
						foreach($newsData as $news){
							if($news['id'] == $id){
								$seacrhItem = $news;
								break;
							}
						}
						$seacrhItem['startlink'] = 'news';
					}
					if($item['attrs']['source_id'] == self::MAGIC_TYPE){
						foreach($magicData as $magic){
							if($magic['id'] == $id){
								$seacrhItem = $magic;
								break;
							}
						}
						$seacrhItem['startlink'] = 'magic';
					}
					if($item['attrs']['source_id'] == self::DIVINATION_TYPE){
						foreach($divinationData as $divination){
							if($divination['id'] == $id){
								$seacrhItem = $divination;
								break;
							}
						}
					}
					$seacrhItem['source_id'] = $item['attrs']['source_id'];
					$result['items'][] = $seacrhItem;
				}
				$pagesCount = $rawData['total_found'] / $this->itemsPerPage;
				if($rawData['total_found'] % $this->itemsPerPage == 0){
					$pagesCount = (int)$pagesCount;
				}else{
					$pagesCount = (int)$pagesCount + 1;
				}
				$this->getPagination($pagesCount, $this->curPage);
			}
		}
		return $result;
	}

	public function searchDreamWordsOnly($query, $page){
		$this->prepareData($page);
		$this->sphinx->SetLimits($this->startResult, $this->itemsPerPage);

		$tokens = explode(' ',$query);
		foreach($tokens as $index => $token){
			$tokens[$index] = "(".$token." | *".$token."*)";
		}
		$query = implode(' ', $tokens);

		$host = str_replace('.','_', $_SERVER['HTTP_HOST']);

		$rawData = $this->sphinx->Query($query,$host . '_dream_word_index');

		$result = array('items' => array(),'total' => $rawData['total_found']);
		//$result = array('items' => array(),'total' => 3);

		$dreamService = App_DreamService::getInstance();

		if($rawData && $rawData['total_found'] > 0){
			if(isset($rawData['matches'])){
				//$result['items'] = $dreamService->getWordsByIds(array(8, 5, 6));
				$result['items'] = $dreamService->getWordsByIds(array_keys($rawData['matches']));

				$pagesCount = $rawData['total_found'] / $this->itemsPerPage;
				if($rawData['total_found'] % $this->itemsPerPage == 0){
					$pagesCount = (int)$pagesCount;
				}else{
					$pagesCount = (int)$pagesCount + 1;
				}
				$this->getPagination($pagesCount, $this->curPage);
			}
		}
		return $result;
	}
	
	public function getPagesArray(){
		return $this->pageArray;
	}
	
	protected function getPagination($countPage, $actPage){
		//если страниц 0 или 1, вернём пустой массив (переключатели не выводятся)
		if ($countPage == 0 || $countPage == 1) return array();
		if ($countPage > 6) //если страниц больше 10, заполним массив pageArray переключателями в зависимости от активной страницы
		{
			//если активная страница - одна из первых  или одна из последних страниц
			//то запишем в массив первые 5 и последние 5 переключателей, разделив их многоточием
			if($actPage <= 4 && $actPage != $countPage/*|| $actPage + 3 >= $countPage*/)
			{
				for($i = 0; $i <= 4; $i++)
				{
					$this->pageArray[$i] = $i + 1;
				}
				$this->pageArray[5] = "...";
				$this->pageArray[6] = $countPage;
			}elseif($actPage == $countPage){
				$this->pageArray[0] = 1;
				$this->pageArray[1] = "...";
				$this->pageArray[2] = $actPage - 4;
				$this->pageArray[3] = $actPage - 3;
				$this->pageArray[4] = $actPage - 2;
				$this->pageArray[5] = $actPage - 1;
				$this->pageArray[6] = $actPage;
			}elseif($actPage + 3 >= $countPage){
				$this->pageArray[0] = 1;
				$this->pageArray[1] = "...";
				for($j = 2, $k = 4; $j <= 6; $j++, $k--)
				{
					$this->pageArray[$j] = $countPage - $k;
				}
			}
				//в противном случае в массив запишем первые и последние две страницы
				//а посередине - пять страниц, с обоих сторон обрамлённых многоточием.
				//активная страница, таким образом, окажется в центре переключателей.
				else
				{
					$this->pageArray[0] = 1;
					$this->pageArray[1] = "...";
					$this->pageArray[2] = $actPage - 1;//$actPage - 2;
					$this->pageArray[3] = $actPage;
					$this->pageArray[4] = $actPage + 1;
					$this->pageArray[5] = "...";
					$this->pageArray[6] = $countPage;
				}
				}
				//если страниц меньше 10, просто заполним массив переключателей всеми номерами страниц подряд
				else
				{
					for($n = 0; $n < $countPage; $n++)
					{
						$this->pageArray[$n] = $n + 1;
					}
				}
	}
}
