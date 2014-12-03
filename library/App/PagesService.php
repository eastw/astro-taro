<?php
class App_PagesService{
	
	protected $pages;
	
	protected $cacheName;
	
	public function __construct(){
		$this->pages = new Application_Model_DbTable_PagesTable();
	}
	
	public function buildPagesQuery(){
		$query = $this->pages->getAdapter()->select();
		return $query->from('pages')->order('id DESC');
	}
	
	public function addPage($data){
		//var_dump($data); die;
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
		$cache->remove('pages');
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
		$cache->remove('pages');
	}
	
	public function getPageById($id){
		return $this->pages->fetchRow($this->pages->getAdapter()->quoteInto('id=?', $id))->toArray();
	}
	
	public function deletePage($id){
		$this->pages->delete($this->pages->getAdapter()->quoteInto('id=?', $id));
		$cache = Zend_Registry::get('cache');
		$cache->remove('pages');
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
		$pages = array();
		if(!$pages = $cache->load('pages',true)){
			$pages = $this->pages->fetchAll(true)->toArray();
			$cache->save($pages,'pages');
		}
		return $pages;
	}
}