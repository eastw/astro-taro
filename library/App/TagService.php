<?php
class App_TagService {
	
	protected $tags;
	
	public function __construct(){
		$this->tags = new Application_Model_DbTable_TagsTable();
	}
	
	public function buildArticleTagsQuery(){
		$query = $this->tags->getAdapter()->select();
		return $query->from('tags')->where('type="a"')->order('id DESC');
	}
	
	public function buildNewsTagsQuery(){
		$query = $this->tags->getAdapter()->select();
		return $query->from('tags')->where('type="n"')->order('id DESC');
	}
	
	public function buildMagicTagsQuery(){
		$query = $this->tags->getAdapter()->select();
		return $query->from('tags')->where('type="m"')->order('id DESC');
	}
	
	public function addArticleTag($data){
		$insertData = array(
				'tagname' => $data['tagname'],
				'description' => $data['description'],
				'seo-keywords' => $data['seokeywords'],
				'seo-description' => $data['seodescription'],
				'type' => 'a',
		);
		$id = $this->tags->insert($insertData);
		
		$updateData = array(
			'alias' => $id.'-'.App_UtilsService::generateTranslit($data['tagname'])
		);
		$this->tags->update($updateData, 'id='.$id);
		
		$this->recacheArticleTags();
	}
	
	public function addNewsTag($data){
		$insertData = array(
				'tagname' => $data['tagname'],
				'description' => $data['description'],
				'seo-keywords' => $data['seokeywords'],
				'seo-description' => $data['seodescription'],
				'type' => 'n',
		);
		$id = $this->tags->insert($insertData);
	
		$updateData = array(
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['tagname'])
		);
		$this->tags->update($updateData, 'id='.$id);
	
		$this->recacheNewsTags();
	}
	
	public function addMagicTag($data){
		$insertData = array(
				'tagname' => $data['tagname'],
				'description' => $data['description'],
				'seo-keywords' => $data['seokeywords'],
				'seo-description' => $data['seodescription'],
				'type' => 'm',
		);
		$id = $this->tags->insert($insertData);
	
		$updateData = array(
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['tagname'])
		);
		$this->tags->update($updateData, 'id='.$id);
	
		$this->recacheMagicTags();
	}
	
	public function recacheArticleTags(){
		$cache = Zend_Registry::get('cache');
		$cache->remove('tags');
		$query = $this->tags->getAdapter()->select();
		$query->from('tags')->where('type="a"');
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		$cache->save($stm->fetchAll(),'tags');
	}
	
	public function recacheNewsTags(){
		$cache = Zend_Registry::get('cache');
		$cache->remove('newstags');
		$query = $this->tags->getAdapter()->select();
		$query->from('tags')->where('type="n"');
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		$cache->save($stm->fetchAll(),'newstags');
	}
	
	public function recacheMagicTags(){
		$cache = Zend_Registry::get('cache');
		$cache->remove('magictags');
		$query = $this->tags->getAdapter()->select();
		$query->from('tags')->where('type="m"');
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		$cache->save($stm->fetchAll(),'magictags');
	}
	
	public function getCachedTags(){
		$cache = Zend_Registry::get('cache');
		return $cache->load('tags');
	}
	
	public function getCachedNewsTags(){
		$cache = Zend_Registry::get('cache');
		return $cache->load('newstags');
	}
	
	public function getCachedMagicTags(){
		$cache = Zend_Registry::get('cache');
		return $cache->load('magictags');
	}
	
	public function getTagById($id){
		return $this->tags->fetchRow($this->tags->getAdapter()->quoteInto('id=?',$id))->toArray();
	}
	
	public function saveArticleTag($data,$id){
		$updateData = array(
				'tagname' => $data['tagname'],
				'description' => $data['description'],
				'seo-keywords' => $data['seokeywords'],
				'seo-description' => $data['seodescription'],
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['tagname']),
		);
		$this->tags->update($updateData, $this->tags->getAdapter()->quoteInto('id=?',$id));
		
		$this->recacheArticleTags();
	}
	
	public function saveNewsTag($data,$id){
		$updateData = array(
				'tagname' => $data['tagname'],
				'description' => $data['description'],
				'seo-keywords' => $data['seokeywords'],
				'seo-description' => $data['seodescription'],
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['tagname']),
		);
		$this->tags->update($updateData, $this->tags->getAdapter()->quoteInto('id=?',$id));
	
		$this->recacheNewsTags();
	}
	
	public function saveMagicTag($data,$id){
		$updateData = array(
				'tagname' => $data['tagname'],
				'description' => $data['description'],
				'seo-keywords' => $data['seokeywords'],
				'seo-description' => $data['seodescription'],
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['tagname']),
		);
		$this->tags->update($updateData, $this->tags->getAdapter()->quoteInto('id=?',$id));
	
		$this->recacheMagicTags();
	}
	
	public function removeTag($id){
		$this->tags->delete($this->tags->getAdapter()->quoteInto('id=?',$id));
		$this->recacheMagicTags();
		$this->recacheArticleTags();
		$this->recacheNewsTags();
	}
	
	public function searchArticleTag($squery){
		$query =$this->tags->getAdapter()->select();
		$query->from('tags')->where('type = "a" AND tagname LIKE \'%'.$squery.'%\' ');
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function searchNewsTag($squery){
		$query =$this->tags->getAdapter()->select();
		$query->from('tags')->where('type = "n" AND tagname LIKE \'%'.$squery.'%\' ');
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function searchMagicTag($squery){
		$query =$this->tags->getAdapter()->select();
		$query->from('tags')->where('type = "m" AND tagname LIKE \'%'.$squery.'%\' ');
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function getAllArticleTags(){
		return $this->tags->fetchAll('type="a"')->toArray();
	}
	
	public function getAllNewsTags(){
		return $this->tags->fetchAll('type="n"')->toArray();
	}
	
	public function getAllMagicTags(){
		return $this->tags->fetchAll('type="m"')->toArray();
	}

	public function getTagByAlias($alias,$tags){
		if(!empty($alias)){
			foreach($tags as $tag){
				if($tag['alias'] == $alias){
					return $tag;
				}
			}
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}else{
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
		
	}
}