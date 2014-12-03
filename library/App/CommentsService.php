<?php
//include_once APPLICATION_PATH . '/../library/ImageTools/AcImage.php';

class App_CommentsService {
	
	protected $comment;
	protected $article;
	protected $divination;
	protected $theme;
	
	
	public function __construct(){
		//$this->db = Zend_Db_Table::getDefaultAdapter();//Zend_Db::factory('PDO_MYSQL', $options);
		$this->comment = new Application_Model_DbTable_CommentTable();
		$this->article = new Application_Model_DbTable_ArticleTable();
		$this->divination = new Application_Model_DbTable_DivinationTable();
		$this->theme = new Application_Model_DbTable_PaythemeTable();
	}
	
	public function buildCommentsQuery($type,$subtype,$sign,$resource,$user){
		$query = $this->comment->select();
		$query->from(array('c'=>'comments'),array(
				'id',
				'body',
				'type',
				'subtype',
				'sign',
				'date_created',
				'user_id',
				'is_spam',
				'abuse',
				'resource_title' => new Zend_Db_Expr(' 
					(CASE
						WHEN (c.type=\'article\' OR c.type=\'news\' OR c.type=\'magic\') THEN (SELECT aa.title FROM article aa WHERE aa.id=c.resource_id)
						WHEN (c.type=\'divination\') THEN (SELECT dd.name FROM divination dd WHERE dd.id=c.resource_id) 
					END) 
				 ')))
			->setIntegrityCheck(FALSE)
			->joinLeft(array('u' => 'users'), 'c.user_id = u.id', array('fullname','email','activity'));
		if($type && $type != 'all'){
			$query->where($this->comment->getAdapter()->quoteInto('c.type=?', $type));
		}
		if($subtype){
			$query->where($this->comment->getAdapter()->quoteInto('c.subtype=?', $subtype));
		}
		if($sign){
			$query->where($this->comment->getAdapter()->quoteInto('c.sign=?', $sign));
		}
		if($user){
			$query->where($this->comment->getAdapter()->quoteInto('c.user_id=?', $user));
		}
		if($resource){
			if($type == 'article' || $type == 'news' || $type == 'magic'){
				$query->joinLeft(array('a' => 'article'), 'c.resource_id = a.id', array('article_id'=>'id','article_title'=>'title'));
			}
			if($type == 'divination'){
				$query->joinLeft(array('d' => 'divination'), 'c.resource_id = d.id', array('divination_id'=>'id','divination_title'=>'name'));
			}
			$query->where($this->comment->getAdapter()->quoteInto('c.resource_id=?', $resource));
		}
		$query->order('c.date_created DESC');
		//var_dump($query->assemble()); die;
		return $query;
	}
	
	public function addComment($data,$userdata){
		if($data['content'] != 'Ваш отзыв'){
			$insertData = array(
				'type' => $this->escape(preg_replace('#(<.*?>)#ims','',$data['type'])),
				'subtype' => $this->escape(preg_replace('#(<.*?>)#ims','',$data['subtype'])),
				'sign' => $this->escape(preg_replace('#(<.*?>)#ims','',$data['sign'])),
				'user_id' => $userdata->id,
				'date_created' => new Zend_Db_Expr('NOW()'),
			);
			
			$body = iconv('UTF-8','windows-1251',$this->escape(preg_replace('#(<.*?>)#ims','',$data['content'])));
			$resource_id = $this->escape(preg_replace('#(<.*?>)#ims','',$data['resource_id']));
			if(empty($resource_id)){
				 $insertData['resource_id'] = new Zend_Db_Expr('NULL');
			}else{
				$insertData['resource_id'] = $resource_id;
			}
			$insertData['body'] = iconv('windows-1251','utf-8',substr($body, 0,450));
			$emailArray = explode('@',$userdata->email);
			$insertData['name'] = $emailArray[0];
			
			$this->comment->getAdapter()->beginTransaction();
			try{
				$id = $this->comment->insert($insertData);
				$updateData = array(
					'comments_count' => new Zend_Db_Expr('comments_count +1')
				);
				$resource = null;
				if($data['type'] != 'numerology' &&  $data['type'] != 'horoscope' && $data['type'] != 'payservice'){
					if($data['type'] == 'article' || $data['type'] == 'magic' || $data['type'] == 'news'){
						$resource = $this->article;
					}
					if($data['type'] == 'divination'){
						$resource = $this->divination;
					}
					$resource->update($updateData, $resource->getAdapter()->quoteInto('id=?',$data['resource_id']));
				}
				
				$query = $this->comment->select()->setIntegrityCheck(FALSE);
				$query->from(array('c' => 'comments'),array('id','body','date_created','name'))
					->joinLeft(array('u' => 'users'), 'c.user_id = u.id',array('avatar'))
					->where('c.id='.$id);
				$stm = $query->query();
				$comment = $stm->fetch();
				$date = $date = new Zend_Date($comment['date_created']);
				if(date('Y-m-d') == date('Y-m-d',strtotime($comment['date_created']))){
					$comment['date_created'] = 'Сегодня&nbsp;в&nbsp;'.date('H:i',strtotime($comment['date_created']));
				}else{
					$comment['date_created'] = $date->toString(Zend_Date::DATE_LONG).'&nbsp;'.date('H:i',strtotime($comment['date_created']));
				}
				$this->comment->getAdapter()->commit();
				//var_dump($comment); die;
				return $comment;
			}catch(Exception $ex){
				//var_dump($ex->getMessage()); die;
				$this->comment->getAdapter()->rollBack();
				return array('id' => '');
			}
		}
		return array('id' => '');
	}
	
	public function getComments($type,$subtype,$sign,$resource_id){
		$adapter = $this->comment->getAdapter();
		$query = $this->comment->select()->setIntegrityCheck(FALSE);
		$query->from(array('c' => 'comments'))
			->joinLeft(array('u' => 'users'), 'c.user_id = u.id',array('avatar'))
			->where($adapter->quoteInto('type=?',$type))
			->where($adapter->quoteInto('subtype=?',$subtype))
			->where($adapter->quoteInto('sign=?',$sign));
			if(!empty($resource_id)){
				$query->where($adapter->quoteInto('resource_id=?',$resource_id));
			}
			$query->order('c.date_created ASC');
			//var_dump($query->assemble()); die;
		$stm = $query->query();
		return $stm->fetchAll();
	}
	
	protected function escape($data){
		if ( !isset($data) or empty($data) ) return '';
		//if ( is_numeric($data) ) return $data;

		$non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
		);
		foreach ( $non_displayables as $regex )
			$data = preg_replace( $regex, '', $data );
		
		$data = str_replace("'", "''", $data );
		return $data;
	}
	
	public function getTypes(){
		return array(
			'article',
			'news',
			'magic',
			'horoscope',
			'divination',
			'numerology',
			'payservice'
		);
	}
	public function getSubTypes($type){
		if($type == 'horoscope'){
			return array(
				array('id' => 'today','value' => 'Гороскоп на сегодня'),
				array('id' => 'business-compability','value' => 'Бизнес совместимость'),
				array('id' => 'love-compability','value' => 'Любовная совместимость'),
				array('id' => 'simple','value' => 'Характеристика знака'),
				array('id' => 'profession','value' => 'Гороскоп профессии'),
				array('id' => 'karma','value' => 'Кармический'),
				array('id' => 'health','value' => 'Гороскоп здоровья'),
				array('id' => 'child','value' => 'Гороскоп ребенка'),
				array('id' => 'business','value' => 'Бизнес гороскоп'),
				array('id' => 'week','value' => 'Гороскоп на неделю'),
				array('id' => 'month','value' => 'Гороскоп на месяц'),
				array('id' => 'year','value' => 'Гороскоп на год'),
			);
		}
		if($type == 'payservice'){
			$payserviceService = new App_PayserviceService();
			$themes = $payserviceService->listThemes();
			$result = array();
			foreach($themes as $theme){
				$result[] = array('id' => $theme['theme_smalltype'],'value' => $theme['theme_name']);
			}
			return $result;
		}
		return array(
			array('id' => 'lifepath','value' => 'Число жизненного пути'),
			array('id' => 'self-expression','value' => 'Число самовыражения'),
			array('id' => 'identity','value' => 'Число личности'),
			array('id' => 'soul','value' => 'Число души'),
			array('id' => 'achievement','value' => 'Важные годы жизни'),
			array('id' => 'karma','value' => 'Кармическая задача'),
			array('id' => 'day','value' => 'Персональный день'),
			array('id' => 'month','value' => 'Персональный месяц'),
			array('id' => 'year','value' => 'Персональный год'),
			array('id' => 'love','value' => 'Любовная совместимость'),
			array('id' => 'partner','value' => 'Партнерская совместимость'),
		);
	}
	public function getSigns(){
		return array(
			'aries'=> 'Овен',
			'taurus'=>'Телец',
			'gemini'=>'Близнецы',
			'cancer'=>'Рак',
			'leo'=>'Лев',
			'virgo'=>'Дева',
			'libra'=>'Весы',
			'scorpio'=>'Скорпион',
			'sagittarius'=>'Стрелец',
			'capricorn'=>'Козерог',
			'aquarius'=>'Водолей',
			'pisces'=>'Рыбы'
		);
	}
	
	public function getUsersByMask($mask){
		$userService = new App_UserService();
		$result = array('users' => $userService->getUsersByMask($mask));
		return $result;
	}
	
	public function getResourcesByMask($mask,$type){
		$result = array('resource' => array()); 
		if($type == 'article' || $type == 'news' || $type == 'magic'){
			$articleService = new App_ArticleService();
			$result['resource'] = $articleService->getArticleByMask($mask,$type); 
		}
		if($type == 'divination'){
			$divinationService = new App_DivinationService();
			$result['resource'] = $divinationService->getDivinationByMask($mask);
		}
		return $result;
	}
	
	public function getResourceByIdAndType($id,$type){
		if($type == 'article' || $type == 'news' || $type == 'magic'){
			$articleService = new App_ArticleService();
			return $articleService->getArticleById($id);//($mask,$type);
		}
		if($type == 'divination'){
			$divinationService = new App_DivinationService();
			$result = $divinationService->getDivinationById($id);
			$result['title'] = $result['name']; 
			return $result;
		}
		return array();
	}
	public function removeComment($id){
		$comment = $this->comment->fetchRow($this->comment->getAdapter()->quoteInto('id=?',$id));
		if($comment){
			$comment = $comment->toArray();
			$this->comment->delete($this->comment->getAdapter()->quoteInto('id=?', $id));
			if(	$comment['type'] == 'article' 
				|| $comment['type'] == 'news' 
				|| $comment['type'] == 'article'
				|| $comment['type'] == 'divination')
			{
				$query = $this->comment->select();
				$query->from(array('comments'),array('comments_count'=>'COUNT(id)'))
					->where($this->comment->getAdapter()->quoteInto('resource_id=?',$comment['resource_id']));
				$stm = $query->query();
				$result = $stm->fetch();
				$updateData = $result;
				$resource = null;
				if($comment['type'] == 'article' 
				|| $comment['type'] == 'news' 
				|| $comment['type'] == 'article')
				{
					$resource = $this->article;
				}
				if($comment['type'] == 'divination')
				{
					$resource = $this->divination;
				}
				if($resource){
					$resource->update($updateData,$resource->getAdapter()->quoteInto('id=?',$comment['resource_id']));
				}
				//var_dump($result); die;	
			}
		}
		
	}
	
	public function removeAllUserComments($userId){
		$comments = $this->comment->fetchAll($this->comment->getAdapter()->quoteInto('user_id=?',$userId));
		if($comments){
			$comments = $comments->toArray();
			foreach($comments as $comment){
				$this->removeComment($comment['id']);
			}
		}
		//$this->comment->delete($this->comment->getAdapter()->quoteInto('user_id=?', $userId));
	}
	
	public function abuseComment($id,$type,$abuse){
		$updateData = array();
		if($type == 'abuse'){
			$updateData['abuse'] = $this->escape($abuse);
		}elseif($type == 'spam'){
			$updateData['is_spam'] = 'y'; 
		}
		$this->comment->update($updateData, $this->comment->getAdapter()->quoteInto('id=?',$id));
	}
	public function getPayserviceComments(){
		$rawComments = $this->getComments('payservice', '', '', '');
		$formattedComments = array('left' => array(),'right' => array());
		foreach ($rawComments as $index => $item){
			if(($index % 2) == 0){
				$formattedComments['left'][] = $item;
			}else{
				$formattedComments['right'][] = $item;
			}
		}
		//echo '<pre>';
		//var_dump($formattedComments); die;
		return $formattedComments;
	}
}