<?php
class App_LayoutService{
	protected $divination;
	protected $article;
	protected $tagService;

	protected $ratingBlockCacheName;
	
	public function __construct(){
		$this->divination = new Application_Model_DbTable_DivinationTable();
		$this->article = new Application_Model_DbTable_ArticleTable();
		$this->tagService = App_TagService::getInstance();

		$this->ratingBlockCacheName = str_replace('.','_', $_SERVER['HTTP_HOST']) . '_rating_block';
	}
	
	public function getRaitingBlockData(){
		$cache = Zend_Registry::get('cache');
		if(!$data = $cache->load($this->ratingBlockCacheName)) {
			$data = $this->getRaitingBlockDataInner();
			$cache->save($data, $this->ratingBlockCacheName);
		}
		return $data;
	}
	
	protected function getRaitingBlockDataInner(){
		$aquery = $this->article->select();
		$aquery->from(array('a'=>'article'),array(
			'id',
			'title',
			'raiting',
			'alias',
			'quicktag',
			'type' => new Zend_Db_Expr('
				(CASE 
					WHEN (a.type = \'a\') THEN (\'article\')
					WHEN (a.type = \'m\') THEN (\'magic\')
				END)
			'),
		))->where('a.activity = \'y\' AND a.type <> \'n\'');
		
		$dquery = $this->divination->select();
		$dquery->from(array('d' => 'divination'),array('id','name','raiting','alias'))
			->setIntegrityCheck(FALSE)
			->joinLeft(array('cc' => 'category'), 'd.category_id = cc.id', array('quicktag'=>'cc.alias'))
			->joinLeft(array('c' => 'category_types'), 'd.type_id = c.id', array('type'))
			->where('d.activity = "y"');

		$select = $this->divination->select();
		$select->union(array($aquery,$dquery))
			->order('raiting DESC')->limit('6');
		$stm = $select->query();
		$result = $stm->fetchAll();
		$tags = array_merge($this->tagService->getAllArticleTags(),$this->tagService->getAllMagicTags());
		foreach($result as $index => $item){
			if($item['type'] == 'article' || $item['type'] == 'magic'){
				$quickIds = explode(';',$item['quicktag']);
				if(!empty($quickIds) && count($quickIds)){
					foreach($tags as $tag){
						if($quickIds[0] == $tag['id']){
							$result[$index]['tag-alias'] = $tag['alias'];
						}
					}
				}
			}
		}
		return $result;
	}
	
}