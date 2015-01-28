<?php
class App_CategoryService{
	
	protected $category;
	
	protected $categoryTypes;
	
	private $structuredCategories;
	
	private $maxId = -1;
	
	const TARO_CATEGORY_TYPE = 1;
	const CLASSIC_CATEGORY_TYPE = 2;
	const RUNE_CATEGORY_TYPE = 3;
	const BOOK_CATEGORY_TYPE = 4;
	const OTHER_CATEGORY_TYPE = 5;
	const LENORMAN_CATEGORY_TYPE = 6;

	private static $instance = null;

	protected $rawCategoryCacheName;
	protected $prestructuredCategoriesCacheName;
	protected $structuredCategoriesCacheName;
	protected $taroListDataCacheName;
	protected $classicListDataCacheName;
	protected $lenormanListDataCacheName;
	protected $runeListDataCacheName;
	protected $bookListDataCacheName;
	protected $otherListDataCacheName;
	protected $categoryTypesCacheName;
	
	private function __construct(){
		$this->category = new Application_Model_DbTable_CategoryTable();
		$this->categoryTypes = new Application_Model_DbTable_CategoryTypesTable();

		$host = str_replace('.','_', $_SERVER['HTTP_HOST']);

		$this->rawCategoryCacheName = $host . '_raw_categories';
		$this->prestructuredCategoriesCacheName = $host . '_prestructured_categories';
		$this->structuredCategoriesCacheName = $host . '_structured_categories';
		$this->taroListDataCacheName = $host . '_taro_list_data';
		$this->classicListDataCacheName = $host . '_classic_list_data';
		$this->lenormanListDataCacheName = $host . '_lenorman_list_data';
		$this->runeListDataCacheName = $host . '_rune_list_data';
		$this->bookListDataCacheName = $host . '_book_list_data';
		$this->otherListDataCacheName = $host . '_other_list_data';
		$this->categoryTypesCacheName = $host . '_category_types';
	}

	public static function getInstance()
	{
		if(is_null(self::$instance)){
			self::$instance = new App_CategoryService();
		}
		return self::$instance;
	}
	
	//TODO: CACHE!
	public function getCategories($filter = null){
		return $this->category->fetchAll()->toArray();
	}
	
	public function structuredCategories(){
		$cache = Zend_Registry::get("cache");
		if(!$categories = $cache->load($this->rawCategoryCacheName,true)){
			$query = $this->category->select()
						->setIntegrityCheck(FALSE)
						->from(array('c' => 'category'))
						->joinLeft(array('t' => 'category_types'), 't.id = c.type_id', array('category_type' => 'type'))
						->order('c.cat_order');
			$stm = $query->query();
			$categories = $stm->fetchAll();
			$cache->save($categories, $this->rawCategoryCacheName);
		}
		if(!$prestructuredCategories = $cache->load($this->prestructuredCategoriesCacheName, true)){
			foreach($categories as $category){
				$prestructuredCategories[$category['parent_id']][] = $category;
			}
			$cache->save($prestructuredCategories,$this->prestructuredCategoriesCacheName);
		}
		if(!$structuredCategories = $cache->load($this->structuredCategoriesCacheName, true)){
			$structuredCategories = $this->buildTree($prestructuredCategories,0);
			$cache->save($structuredCategories,$this->structuredCategoriesCacheName);
		}
		return $structuredCategories;
	}
	
	public function prestructuredCategories(){
		$cache = Zend_Registry::get("cache");
		if(!$categories = $cache->load($this->rawCategoryCacheName, true)){
			$query = $this->category->select()
				->from($this->category)->order('cat_order');
			$stm = $query->query();
			$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
			$categories = $stm->fetchAll();
			$cache->save($categories,$this->rawCategoryCacheName);
		}
		if(!$prestructuredCategories = $cache->load($this->prestructuredCategoriesCacheName, true)){
			foreach($categories as $category){
				$prestructuredCategories[$category['parent_id']][] = $category;
			}
			$cache->save($prestructuredCategories,$this->prestructuredCategoriesCacheName);
		}
		return $prestructuredCategories;
	}
	
	public function flatCategories(){
		$categories = $this->structuredCategories();
		$flatCategories = array();
		foreach($categories as $category){
			if(isset($category['children']) && count($category['children'])){
				foreach($category['children'] as $child){
					$flatCategories[] = array(
							'id' => $child['attr']['id'],
							'category' => $category['data'].' / '.$child['data']
					);
				}
			}else{
				$flatCategories[] = array(
					'id' => $category['attr']['id'],
					'category' => $category['data']
				);
			}
		}
		return $flatCategories;
	}
	
	private function buildTree($cats,$parent_id){
		$nodes = array();
		if(is_array($cats) && isset($cats[$parent_id])){
			foreach($cats[$parent_id] as $cat){
				$node = array();
				$node['data'] = $cat['name'];
				$node['attr'] = array('id'=>$cat['id'], 'description' => $cat['description'] );
				$node['state'] = 'open';
				$node['metadata'] = array('catdesc' => $cat['description'],'keywords' => $cat['seo-keywords'],'description' => $cat['seo-description'],'cat_type' => $cat['type_id'],'minidesc' => $cat['minidesc'],'image' => $cat['image'],'alias' => $cat['alias'],'category_type' => $cat['category_type']);
				$children = $this->buildTree($cats,$cat['id']);
				if(null != $children){
					$node['children'] = $children; 
				}
				$nodes[] = $node;
			}
		}
		else return null;
		return $nodes;
	}
	
	public function saveCategories($categories){
		$this->category->delete('true');
		$this->saveTree($categories, 0);
		$cache = Zend_Registry::get("cache");
		$cache->remove($this->rawCategoryCacheName);
		$cache->remove($this->prestructuredCategoriesCacheName);
		$cache->remove($this->structuredCategoriesCacheName);
		
		$types = $this->getCategoryTypes();
		foreach($types as $type){
			$cache->remove(str_replace('.','_', $_SERVER['HTTP_HOST']) . '_' . $type['type'].'_list_data');
		}
		$this->structuredCategories();
	}
	
	private function saveTree($cats,$parent_id){
		if(is_array($cats)){
			foreach($cats as $index => $cat){
				$id = -1;
				$insert = array(
					//'id' => $cat['id'],
					'name' => $cat['data'],
					//'description' => $cat['description'],
					'alias' => App_UtilsService::generateTranslit($cat['data']),
					'cat_order' => $index,
					'parent_id' => $parent_id,
					'type_id' => (!empty($cat['metadata']['cat_type']))?$cat['metadata']['cat_type']:0,	
				);
				if(isset($cat['attr']) && isset($cat['attr']['id']) && $cat['attr']['id'] != 'new'){
					$insert['id'] = $cat['attr']['id'];
				}
				if(isset($cat['metadata']) && isset($cat['metadata']['catdesc']) ){
					$insert['description'] = $cat['metadata']['catdesc'];
				}
				if(isset($cat['metadata']) && isset($cat['metadata']['keywords']) ){
					$insert['seo-keywords'] = $cat['metadata']['keywords'];
				}
				if(isset($cat['metadata']) && isset($cat['metadata']['description']) ){
					$insert['seo-description'] = $cat['metadata']['description'];
				}
				if(isset($cat['metadata']) && isset($cat['metadata']['minidesc']) ){
					$insert['minidesc'] = $cat['metadata']['minidesc'];
				}
				if(isset($cat['metadata']) && isset($cat['metadata']['image']) ){
					$insert['image'] = $cat['metadata']['image'];
				}
				$id = $this->category->insert($insert);
				if(isset($cat['children'])){
					$this->saveTree($cat['children'],$id);
				}
			}
		}
	}
	
	public function getCategory($id){
		return $this->category->fetchRow($this->category->getAdapter()->quoteInto('id=?', $id));
	}
	
	public function removeCategory($id){
		$where = $this->category->getAdapter()->quoteInto('id=?',$id);
		$this->category->delete($where);
	}
	
	public function time(){
		/*
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		//$mtime = $mtime[1] + $mtime[0];
		return $mtime[1] + $mtime[0];
		*/
		/*list($usec, $seconds) = explode(" ", microtime());
		return ((float)$usec + (float)$seconds);
		*/
		return microtime(true);
	}
	
	public function getCategoryTypes(){
		$cache = Zend_Registry::get('cache');
		if(!$types = $cache->load($this->categoryTypesCacheName,true)){
			$types = $this->categoryTypes->fetchAll()->toArray();
			$cache->save($types, $this->categoryTypesCacheName);
		}
		return $types;
	}
	
	public function getChildCategoriesByType($typeId){
		$query = $this->category->select();
		$query->from('category')->where($this->category->getAdapter()->quoteInto('type_id=? AND active = \'y\' AND parent_id <> 0', $typeId))->order('cat_order');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	public function getRootCategoriesByType($typeId){
		$query = $this->category->select();
		$query->from('category')->where($this->category->getAdapter()->quoteInto('type_id=? AND active = \'y\' AND parent_id = 0', $typeId))->order('cat_order');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function updateCategoryImage($catId,$image){
		$updateData = array(
			'image' => $image,
		);
		$this->category->update($updateData, $this->category->getAdapter()->quoteInto('id=?', $catId));
		
		$cache = Zend_Registry::get("cache");
		$cache->remove($this->rawCategoryCacheName);
		$cache->remove($this->prestructuredCategoriesCacheName);
		$cache->remove($this->structuredCategoriesCacheName);
		$cache->remove($this->taroListDataCacheName);
		$cache->remove($this->classicListDataCacheName);
		$cache->remove($this->lenormanListDataCacheName);
		$cache->remove($this->runeListDataCacheName);
		$cache->remove($this->bookListDataCacheName);
		$cache->remove($this->otherListDataCacheName);
		
	}
}