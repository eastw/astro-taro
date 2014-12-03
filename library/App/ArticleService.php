<?php
//include_once APPLICATION_PATH . '/../library/ImageTools/AcImage.php';

class App_ArticleService {
	
	protected $articles;
	
	protected $articleTags;
	
	protected $db;
	
	public function __construct(){
		$this->db = Zend_Db_Table::getDefaultAdapter();//Zend_Db::factory('PDO_MYSQL', $options);
		
		$this->articles = new Application_Model_DbTable_ArticleTable();
		$this->articleTags = new Application_Model_DbTable_ArticleTagsTable();
	}
	
	public function getDb(){
		return $this->db;
	}
	
	public function buildArticlesQuery(){
		$query = $this->articles->getAdapter()->select();
		return $query->from('article')->where('type="a"')->order('date_created DESC');
	}
	
	public function builNewsQuery(){
		$query = $this->articles->getAdapter()->select();
		return $query->from('article')->where('type="n"')->order('date_created DESC');
	}
	
	public function buildMagicQuery(){
		$query = $this->articles->getAdapter()->select();
		return $query->from('article')->where('type="m"')->order('date_created DESC');
	}
	
	public function buildArticleAnonseQuery($type){
		$query = $this->articles->getAdapter()->select();
		$query->from(array('a' => 'article','t' => 'tags'),
					array('id','title','alias','image','anonse','date_created','quicktag','raiting','comments_count'))
					->where('a.type="a" AND a.activity="y" AND a.show_in_list="y" ');
		switch($type){
			case 'all': $query->order('date_created DESC'); break;
			case 'popular': $query->order('raiting DESC'); break;
		}
				//->order('date_created DESC');
		//var_dump($query->assemble()); die;
		return $query;		
	}
	
	public function buildNewsAnonseQuery($type){
		$query = $this->articles->getAdapter()->select();
		$query->from(array('a' => 'article','t' => 'tags'),
				array('id','title','alias','image','anonse','date_created','quicktag','raiting','comments_count'))
				->where('a.type="n" AND a.activity="y"');
		switch($type){
			case 'all': $query->order('date_created DESC'); break;
			case 'popular': $query->order('raiting DESC'); break;
		}
		//var_dump($query->assemble()); die;
		return $query;
	}
	
	public function buildMagicAnonseQuery($type){
		$query = $this->articles->getAdapter()->select();
		$query->from(array('a' => 'article','t' => 'tags'),
				array('id','title','alias','image','anonse','date_created','quicktag','raiting','comments_count'))
				->where('a.type="m" AND a.activity="y"');
		switch($type){
			case 'all': $query->order('date_created DESC'); break;
			case 'popular': $query->order('raiting DESC'); break;
		}
		return $query;
	}
	
	public function buildTagArticlesQuery($tag){
		$query = $this->articles->getAdapter()->select();
		$subquery = $this->articles->getAdapter()->select();
		$subquery->from('article_tags',array('article_id'))->where($this->articles->getAdapter()->quoteInto('tag_id=?', $tag));
		
		$query->from(array('a' => 'article'),
				array('id','title','alias','image','anonse','date_created','quicktag','raiting','comments_count'))
				->where('a.id IN ? AND a.type="a" AND a.activity = "y" AND a.show_in_list="y"',$subquery)
				->order('date_created DESC');
		//var_dump($query->assemble()); die;
		return $query;
	}
	
	public function buildTagNewsQuery($tag){
		$query = $this->articles->getAdapter()->select();
		$subquery = $this->articles->getAdapter()->select();
		$subquery->from('article_tags',array('article_id'))->where($this->articles->getAdapter()->quoteInto('tag_id=?', $tag));
	
		$query->from(array('a' => 'article'),
				array('id','title','alias','image','anonse','date_created','quicktag','raiting','comments_count'))
				->where('a.id IN ? AND a.type="n" AND a.activity = "y"',$subquery)
				->order('date_created DESC');
		//var_dump($query->assemble()); die;
		return $query;
	}
	
	public function buildTagMagicQuery($tag){
		$query = $this->articles->getAdapter()->select();
		$subquery = $this->articles->getAdapter()->select();
		$subquery->from('article_tags',array('article_id'))->where($this->articles->getAdapter()->quoteInto('tag_id=?', $tag));
	
		$query->from(array('a' => 'article'),
				array('id','title','alias','image','anonse','date_created','quicktag','raiting','comments_count'))
				->where('a.id IN ? AND a.type="m" AND a.activity = "y"',$subquery)
				->order('date_created DESC');
		//var_dump($query->assemble()); die;
		return $query;
	}
	
	public function addArticle($data){
		$insertData = array(
				'title' => $data['title'],
				'image' => $data['image'],
				'anonse' => $data['anonse'],
				'content' => $data['content'],
				'activity' => $data['activity'],
				'meta_keys' => $data['seokeywords'],
				'meta_desc' => $data['seodescription'],
				'date_created' => new Zend_Db_Expr('NOW()'),
				'type' => 'a',
				'show_in_list' => $data['show_in_list'],
		);
		
		//TODO: if size more than 200x120 px - resize
		/*
		$path = APPLICATION_PATH.'/../public/files/articles/';
		$img = AcImage::createImage($path.$data['image']);
		$img->resize(200, 120);
		$img->save($path.$data['image']);
		*/
		
		$id = $this->articles->insert($insertData);
		$tags = '';
		foreach($data['tags'] as $index => $tag){
			$tags .= $index;
			$tags .= ';';
		}
		$updateData = array(
			'alias' => $id.'-'.App_UtilsService::generateTranslit($data['title']),
			'quicktag' => $tags
		);
		
		$this->articles->update($updateData, 'id='.$id);
		
		foreach($data['tags'] as $index => $tag){
			$insertData = array(
				'article_id' => $id,
				'tag_id' => $index
			);
			
			$this->articleTags->insert($insertData);
		}
	}
	
	public function addNews($data){
		$insertData = array(
				'title' => $data['title'],
				'image' => $data['image'],
				'anonse' => $data['anonse'],
				'content' => $data['content'],
				'activity' => $data['activity'],
				'meta_keys' => $data['seokeywords'],
				'meta_desc' => $data['seodescription'],
				'date_created' => new Zend_Db_Expr('NOW()'),
				'type' => 'n',
				
		);
	
		//TODO: if size more than 200x120 px - resize
		/*
			$path = APPLICATION_PATH.'/../public/files/articles/';
		$img = AcImage::createImage($path.$data['image']);
		$img->resize(200, 120);
		$img->save($path.$data['image']);
		*/
	
		$id = $this->articles->insert($insertData);
		$tags = '';
		foreach($data['tags'] as $index => $tag){
			$tags .= $index;
			$tags .= ';';
		}
		$updateData = array(
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['title']),
				'quicktag' => $tags
		);
	
		$this->articles->update($updateData, 'id='.$id);
	
		foreach($data['tags'] as $index => $tag){
			$insertData = array(
					'article_id' => $id,
					'tag_id' => $index
			);
				
			$this->articleTags->insert($insertData);
		}
	}
	
	public function addMagic($data){
		$insertData = array(
				'title' => $data['title'],
				'image' => $data['image'],
				'anonse' => $data['anonse'],
				'content' => $data['content'],
				'activity' => $data['activity'],
				'meta_keys' => $data['seokeywords'],
				'meta_desc' => $data['seodescription'],
				'date_created' => new Zend_Db_Expr('NOW()'),
				'type' => 'm',
		);
	
		//TODO: if size more than 200x120 px - resize
		/*
		 $path = APPLICATION_PATH.'/../public/files/articles/';
		$img = AcImage::createImage($path.$data['image']);
		$img->resize(200, 120);
		$img->save($path.$data['image']);
		*/
	
		$id = $this->articles->insert($insertData);
		$tags = '';
		foreach($data['tags'] as $index => $tag){
			$tags .= $index;
			$tags .= ';';
		}
		$updateData = array(
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['title']),
				'quicktag' => $tags
		);
	
		$this->articles->update($updateData, 'id='.$id);
	
		foreach($data['tags'] as $index => $tag){
			$insertData = array(
					'article_id' => $id,
					'tag_id' => $index
			);
	
			$this->articleTags->insert($insertData);
		}
	}
	
	public function saveArticle($data,$id){
		$tags = '';
		foreach($data['tags'] as $index => $tag){
			$tags .= $index;
			$tags .= ';';
		}
		$updateData = array(
				'title' => $data['title'],
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['title']),
				'image' => $data['image'],
				'anonse' => $data['anonse'],
				'content' => $data['content'],
				'activity' => $data['activity'],
				'meta_keys' => $data['seokeywords'],
				'meta_desc' => $data['seodescription'],
				'quicktag' => $tags,
				'show_in_list' => $data['show_in_list'],
		);
		
		$this->articles->update($updateData, 'id='.$id);
		$where = $this->articles->getAdapter()->quoteInto('article_id=?', $id);
		$this->articleTags->delete($where);
		
		foreach($data['tags'] as $index => $tags){
			$insertData = array(
					'article_id' => $id,
					'tag_id' => $index
			);
			$this->articleTags->insert($insertData);
		}
	}
	
	public function saveNews($data,$id){
		$tags = '';
		foreach($data['tags'] as $index => $tag){
			$tags .= $index;
			$tags .= ';';
		}
		$updateData = array(
				'title' => $data['title'],
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['title']),
				'image' => $data['image'],
				'anonse' => $data['anonse'],
				'content' => $data['content'],
				'activity' => $data['activity'],
				'meta_keys' => $data['seokeywords'],
				'meta_desc' => $data['seodescription'],
				'quicktag' => $tags
		);
	
		$this->articles->update($updateData, 'id='.$id);
		$where = $this->articles->getAdapter()->quoteInto('article_id=?', $id);
		$this->articleTags->delete($where);
	
		foreach($data['tags'] as $index => $tags){
			$insertData = array(
					'article_id' => $id,
					'tag_id' => $index
			);
			$this->articleTags->insert($insertData);
		}
	}
	
	public function saveMagic($data,$id){
		$tags = '';
		foreach($data['tags'] as $index => $tag){
			$tags .= $index;
			$tags .= ';';
		}
		$updateData = array(
				'title' => $data['title'],
				'alias' => $id.'-'.App_UtilsService::generateTranslit($data['title']),
				'image' => $data['image'],
				'anonse' => $data['anonse'],
				'content' => $data['content'],
				'activity' => $data['activity'],
				'meta_keys' => $data['seokeywords'],
				'meta_desc' => $data['seodescription'],
				'quicktag' => $tags,
		);
	
		$this->articles->update($updateData, 'id='.$id);
		$where = $this->articles->getAdapter()->quoteInto('article_id=?', $id);
		$this->articleTags->delete($where);
	
		foreach($data['tags'] as $index => $tags){
			$insertData = array(
					'article_id' => $id,
					'tag_id' => $index
			);
			$this->articleTags->insert($insertData);
		}
	}
	
	public function getArticleById($id){
		$article = $this->articles->fetchRow($this->articles->getAdapter()->quoteInto('id=?', $id));//->toArray();
		$tags = $article->findManyToManyRowset('Application_Model_DbTable_TagsTable', 'Application_Model_DbTable_ArticleTagsTable')->toArray();
		$article = $article->toArray();
		$article['tags'] = $tags;
		return $article; 
	}
	
	public function getArticleByAlias($alias){
		$aliasParts = explode('-',$alias);
		$id = $aliasParts[0];
		$article = $this->articles->fetchRow($this->articles->getAdapter()->quoteInto('id=?', $id))->toArray();
		
		if($article['alias'] !== $alias){
			throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
		}
		return $article;
	}
	
	public function deleteArticle($id){
		$this->articles->delete($this->articles->getAdapter()->quoteInto('id=?', $id));
	}
	
	public function searchArticle($squery){
		$query =$this->articles->getAdapter()->select();
		if(!empty($squery)){
			$query->from('article')->where('type="a" AND title LIKE \'%'.$squery.'%\' ');
		}else{
			$query->from('article');
		}
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function searchNews($squery){
		$query =$this->articles->getAdapter()->select();
		if(!empty($squery)){
			$query->from('article')->where('type="n" AND title LIKE \'%'.$squery.'%\' ');
		}else{
			$query->from('article');
		}
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function searchMagic($squery){
		$query =$this->articles->getAdapter()->select();
		if(!empty($squery)){
			$query->from('article')->where('type="m" AND title LIKE \'%'.$squery.'%\' ');
		}else{
			$query->from('article');
		}
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function setVote($id){
		$updateData = array(
			'raiting' => new Zend_Db_Expr('raiting + 1')
		);
		$this->articles->update($updateData,$this->articles->getAdapter()->quoteInto('id=?', $id));
	}
	
	public function getLastMagic(){
		$query =$this->articles->getAdapter()->select();
		$query->from('article')->where('type="m"')->order('date_created DESC')->limit(6);
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function getLastArticles(){
		$query =$this->articles->getAdapter()->select();
		$query->from('article')->where('type="a" AND show_in_list="y"')->order('date_created DESC')->limit(10);
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function getLastNews(){
		$query =$this->articles->getAdapter()->select();
		$query->from('article')->where('type="n"')->order('date_created DESC')->limit(5);
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function getArticleByMask($mask,$type){
		$query =$this->articles->getAdapter()->select();
		$query->from(array('a' => 'article'),array('id','title'))
			->where('show_in_list="y"');
			if($type == 'article'){
				$query->where('type="a"');
			}
			if($type == 'news'){
				$query->where('type="n"');
			}
			if($type == 'magic'){
				$query->where('type="m"');
			}
			
			$query->where('title LIKE ?','%'.$mask.'%');
		//var_dump($query->assemble()); die;	
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
}