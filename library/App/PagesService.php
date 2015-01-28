<?php
class App_PagesService{
	
	protected $pages;
	
	protected $pagesCacheName;
	
	public function __construct(){
		$this->pages = new Application_Model_DbTable_PagesTable();
		$this->pagesCacheName = str_replace('.','_', $_SERVER['HTTP_HOST']) . '_pages';
	}
	
	public function buildPagesQuery(){
		$query = $this->pages->getAdapter()->select();
		return $query->from('pages')->order('id DESC');
	}
	
	public function addPage($data){
		$insertData = array(
			'url' => $data['url'],
			'page_name_ru' => $data['name_ru'],
			'title' => $data['title'],
			'keywords' => $data['seokeywords'],
			'description' => $data['seodescription'],
			'minidesc' => $data['minidesc'],
		);
		$this->pages->insert($insertData);
		$cache = Zend_Registry::get('cache');
		$cache->remove($this->pagesCacheName);
	}
	
	public function savePage($data,$id){
		$updateData = array(
			'url' => $data['url'],
			'page_name_ru' => $data['name_ru'],
			'title' => $data['title'],
			'keywords' => $data['seokeywords'],
			'description' => $data['seodescription'],
			'minidesc' => $data['minidesc'],
		);
		$this->pages->update($updateData,$this->pages->getAdapter()->quoteInto('id=?', $id));
		$cache = Zend_Registry::get('cache');
		$cache->remove($this->pagesCacheName);
	}
	
	public function getPageById($id){
		return $this->pages->fetchRow($this->pages->getAdapter()->quoteInto('id=?', $id))->toArray();
	}
	
	public function deletePage($id){
		$this->pages->delete($this->pages->getAdapter()->quoteInto('id=?', $id));
		$cache = Zend_Registry::get('cache');
		$cache->remove($this->pagesCacheName);
	}
	public function searchPage($squery){
		$query =$this->pages->getAdapter()->select();
		if(!empty($squery)){
			$query->from('pages')->where('page_name_ru LIKE \'%'.$squery.'%\'');
		}else{
			$query->from('pages');
		}
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function getAllPages(){
		$cache = Zend_Registry::get('cache');
		if(!$pages = $cache->load($this->pagesCacheName, true)){
			$pages = $this->pages->fetchAll(true)->toArray();
			$cache->save($pages, $this->pagesCacheName);
		}
		return $pages;
	}
}