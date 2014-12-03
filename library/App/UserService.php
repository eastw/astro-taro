<?php
class App_UserService {
	
	const ADMIN_ROLE  = 'admin';
	const USER_ROLE = 'user';
	
	protected $users;
	
	protected $_userData = array(
				'id','email','role','register_date',
				'birthday','activity','fullname','gender',
				'last_login','avatar','horoscope_karma_id',
				'sun_sign_id','china_sign_id','china_sign_type_id',
				'kelt_sign_id','taro_day','rune_day','hexagramm_day',
				'number_day','taro_day_state','rune_day_state','sun_sign_alias','social_id');
	
	protected $template_symbols = array(1,3,5,0,2,5,2,1,4);
	
	
	public function __construct(){
		$this->users = new Application_Model_DbTable_Users();
	}
	
	public function auth($data){
		//var_dump($data); die;
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		
		$salt = $this->createSalt($data['pass']);
		
		$authAdapter->setTableName('users')
					->setIdentityColumn('email')
					->setCredentialColumn('pass')
					->setCredentialTreatment("MD5(CONCAT(MD5(?),'$salt',MD5('$salt')))");
		$authAdapter->setIdentity($data['email'])
					->setCredential($data['pass']);
		
		//var_dump(Zend_Auth::getInstance()->hasIdentity()); die;
		
		Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('astroauth'));
		
		$result = Zend_Auth::getInstance()->authenticate($authAdapter);
		
		if ($result->isValid()) {
			$resultObject = $authAdapter->getResultRowObject($this->_userData);
			$updateData = array(
				'last_login' => new Zend_Db_Expr('NOW()'),
			);
			//$fullname = 
			$this->users->update($updateData, 'id='.$resultObject->id);
			$resultObject->last_login = date('Y-m-d H:i:s');
			Zend_Auth::getInstance()->getStorage()->write($resultObject);
		}
		
		return $result;
	}
	
	public function addUser($data){
		$salt = $this->createSalt($data['pass']);
		$insertData = array(
				'pass' => md5(md5($data['pass']).$salt.md5($salt)),
				'email' => $data['email'],
				'activity' => 'y',
				'role' => 'user',
				'birthday' => (isset($data['byear']) && !empty($data['byear']))?($data['byear'].'-'.$data['bmonth'].'-'.$data['bday']):new Zend_Db_Expr('NULL'),
				'register_date' => new Zend_Db_Expr('NOW()'),
				'fullname' => (isset($data['fname']))?($data['fname'].':'.$data['mname'].':'.$data['lname']):'',
				'gender' => (isset($data['gender']))?$data['gender']:'n',
		);
		if(isset($data['byear']) && !empty($data['byear'])){
			$horoscopeService = new App_HoroscopeService();
			$sign = $horoscopeService->getSunSignByBirthday($data['byear'].'-'.$data['bmonth'].'-'.$data['bday']);
			$insertData['sun_sign_id'] = $sign['id'];
			$insertData['sun_sign_alias'] = $sign['sign'];
		}
		$this->users->insert($insertData);
		
		$mailer = new App_HtmlMailer();
		$mailer->setSubject('Регистрация на сайте AstroTarot.ru')
			->setViewParam('login',$data['email'])
			->setViewParam('password',$data['pass'])
			->addTo($data['email'])->sendHtmlTemplate('registration.phtml');
	}
	
	public function addVKUser($data,$mid){
		$pass = uniqid();
		$salt = $this->createSalt($pass);
		
		$insertData = array(
				'pass' => md5(md5($pass).$salt.md5($salt)),
				'email' => 'не заполнено',
				'activity' => 'y',
				'role' => 'user',
				'register_date' => new Zend_Db_Expr('NOW()'),
				'fullname' => (!empty($data['fname']))?($data['fname'].':не заполнено:'.$data['lname']):'',
				//'gender' => (!empty($data['sex']) && $data['sex'] == '2')?'m':(!empty($data['sex']) && $data['sex'] != '2')?'f':'n',
				'social_id' => $mid
		);
		if(!empty($data['sex']) && $data['sex'] == '2'){
			$insertData['gender'] = 'm';
		}else{
			if(!empty($data['sex']) && $data['sex'] != '2'){
				$insertData['gender'] = 'f';
			}else{
				$insertData['gender'] = 'n';
			}
		}
		if(!empty($data['bdate'])){
			$insertData['birthday'] = date('Y-m-d',strtotime($data['bdate']));
			
			$horoscopeService = new App_HoroscopeService();
			$sign = $horoscopeService->getSunSignByBirthday(date('Y',strtotime($data['bdate'])).'-'.date('m',strtotime($data['bdate'])).'-'.date('d',strtotime($data['bdate'])));
			$insertData['sun_sign_id'] = $sign['id'];
			$insertData['sun_sign_alias'] = $sign['sign'];
		}
		//var_dump($insertData); die;
		$this->users->insert($insertData);
		
		$insertData['pass'] = md5(md5($pass).$salt.md5($salt));
		$this->authSocialUser($insertData);
	}
	
	public function addFBUser($data){
		$pass = uniqid();
		$salt = $this->createSalt($pass);
		
		$insertData = array(
				'pass' => md5(md5($pass).$salt.md5($salt)),
				'email' => $data['email'],
				'activity' => 'y',
				'role' => 'user',
				'register_date' => new Zend_Db_Expr('NOW()'),
				'fullname' => $data['fname'].(!empty($data['mname'])?':'.$data['mname'].':':':не заполнено:').$data['lname'],
				'social_id' => $data['social_id']
		);
		
		
		if(!empty($data['sex']) && $data['sex'] == 'male'){
			$insertData['gender'] = 'm';
		}else{
			if(!empty($data['sex']) && $data['sex'] != 'male'){
				$insertData['gender'] = 'f';
			}else{
				$insertData['gender'] = 'n';
			}
		}
		if(!empty($data['bdate'])){
			$insertData['birthday'] = $data['bdate']->format('Y-m-d');//date('Y-m-d',strtotime($data['bdate']));
			
			$horoscopeService = new App_HoroscopeService();
			$sign = $horoscopeService->getSunSignByBirthday($data['bdate']->format('Y-m-d'));
			$insertData['sun_sign_id'] = $sign['id'];
			$insertData['sun_sign_alias'] = $sign['sign'];
		}
		//var_dump($insertData); die;
		$this->users->insert($insertData);
		
		$insertData['pass'] = md5(md5($pass).$salt.md5($salt));
		$this->authSocialUser($insertData);
	}
	
	public function addTwitterUser($data){
		$pass = uniqid();
		$salt = $this->createSalt($pass);
		
		$insertData = array(
				'pass' => md5(md5($pass).$salt.md5($salt)),
				'email' => 'не заполнено',
				'activity' => 'y',
				'role' => 'user',
				'register_date' => new Zend_Db_Expr('NOW()'),
				'social_id' => $data['social_id']
		);
		
		if(!empty($data['fname']) && !empty($data['lname'])){
			$insertData['fullname'] = $data['fname'].':не заполнено:'.$data['lname'];
		}else{
			$insertData['fullname'] = 'не заполнено:не заполнено:не заполнено';
		}
		$this->users->insert($insertData);
		
		$insertData['pass'] = md5(md5($pass).$salt.md5($salt));
		$this->authSocialUser($insertData);
	}
	
	public function socialUserExist($mid){
		$user = $this->users->fetchRow($this->users->getAdapter()->quoteInto('social_id=?',$mid));
		if($user){
			return true;
		}
		return false;
	}
	
	protected function authSocialUser($data){
		//var_dump($data);
		//$salt = $this->createSalt($pass);
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		$authAdapter->setTableName('users')
					->setIdentityColumn('social_id')
					->setCredentialColumn('pass');
					//->setCredentialTreatment("MD5(CONCAT(MD5(?),'$salt',MD5('$salt')))");
		$authAdapter->setIdentity($data['social_id'])
					->setCredential($data['pass']);
					
		Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('astroauth'));
		
		$result = Zend_Auth::getInstance()->authenticate($authAdapter);
		
		if ($result->isValid()) {
			$resultObject = $authAdapter->getResultRowObject($this->_userData);
			//var_dump($resultObject); die;
			$updateData = array(
				'last_login' => new Zend_Db_Expr('NOW()'),
			);
			$this->users->update($updateData, 'id='.$resultObject->id);
			$resultObject->last_login = date('Y-m-d H:i:s');
			Zend_Auth::getInstance()->getStorage()->write($resultObject);
		}
	}
	
	
	public function autorizeVKUser($socialId){
		$user = $this->users->fetchRow($this->users->getAdapter()->quoteInto('social_id=?',$socialId))->toArray();
		$user['social_id'] = $socialId;
		$this->authSocialUser($user);
	}
	
	public function autorizeFBUser($socialId){
		$user = $this->users->fetchRow($this->users->getAdapter()->quoteInto('social_id=?',$socialId))->toArray();
		$this->authSocialUser($user);
	}
	
	public function autorizeTwitterUser($socialId){
		$user = $this->users->fetchRow($this->users->getAdapter()->quoteInto('social_id=?',$socialId))->toArray();
		$this->authSocialUser($user);
	}
	
	
	public function saveUser($data,$id){
		$storage = Zend_Auth::getInstance()->getStorage()->read();
		$updateData = array(
				'email' => $data['email'],
		);
		$storage->email = $data['email'];
		
		if(!empty($data['byear']) && !empty($data['bmonth']) && !empty($data['bday'])){
			$updateData['birthday'] = $data['byear'].'-'.$data['bmonth'].'-'.$data['bday'];
			$updateData['horoscope_karma_id'] = new Zend_Db_Expr('NULL');
			$updateData['sun_sign_id'] = new Zend_Db_Expr('NULL');
			$updateData['china_sign_id'] = new Zend_Db_Expr('NULL');
			$updateData['china_sign_type_id'] = new Zend_Db_Expr('NULL');
			$updateData['kelt_sign_id'] = new Zend_Db_Expr('NULL');
			$updateData['lifenumber'] = new Zend_Db_Expr('NULL');
			$storage->birthday = $data['byear'].'-'.$data['bmonth'].'-'.$data['bday'];
			$storage->horoscope_karma_id = 0;
			$storage->sun_sign_id = 0;
			$storage->china_sign_id = 0;
			$storage->china_sign_type_id = 0;
			$storage->kelt_sign_id = 0;
			$storage->lifenumber = 0;
			//TODO: need sun sign id and sun alias
			
		}
		//var_dump($data); die;
		if((!empty($data['fname']) && $data['fname'] != 'не заполнено')  
			&& (!empty($data['mname']) && $data['mname'] != 'не заполнено' ) 
			&& (!empty($data['lname']) && $data['lname'] != 'не заполнено')){
			$updateData['fullname'] = $data['fname'].':'.$data['mname'].':'.$data['lname'];
			$storage->fullname = $data['fname'].':'.$data['mname'].':'.$data['lname'];
		}
		if(!empty($data['gender'])){
			$updateData['gender'] = $data['gender'];
			$storage->gender = $data['gender'];
		}
		/*
		$updateData['forum_nik'] = preg_replace('#(<.*?>)#ims','',$data['nik']);
		$updateData['forum_signature'] = preg_replace('#(<.*?>)#ims','',$data['signature']);
		*/		
		
		if(!empty($data['pass'])){
			$salt = $this->createSalt($data['pass']);
			$updateData['pass'] = md5(md5($data['pass']).$salt.md5($salt));
		}
		//var_dump($updateData); die;
		$this->users->update($updateData, $this->users->getAdapter()->quoteInto('id=?', $id));
		Zend_Auth::getInstance()->getStorage()->write($storage);
		
	}
	
	private function createSalt($password){
		$salt = '$1$';
		if(strlen($password) < 6) $password = '-#@*5+~';
		for( $i = 0; $i < 9; $i++){
			$salt .= $password[$this->template_symbols[$i]];
		}
		$salt .= '$';
		return $salt;
	}
	
	public function recoverPassword($data){
		$email = $data['remail'];
		//$user = $this->users->fetchRow($this->users->getAdapter()->quoteInto('email=?',$data['remail']))->toArray();
		//$user['password'] =
		$pass = uniqid();
		$salt = $this->createSalt($pass);
		$updateData = array(
			'pass' => md5(md5($pass).$salt.md5($salt))
		);
		$this->users->update($updateData,$this->users->getAdapter()->quoteInto('email=?',$data['remail']));
		
		$mailer = new App_HtmlMailer();
		$mailer->setSubject('Восстановление пароля на сайте AstroTarot.ru')
			->setViewParam('login',$data['remail'])
			->setViewParam('password',$pass)
			->addTo($data['remail'])->sendHtmlTemplate('recover.phtml');
		
		//$user['pass'] = md5(md5($data['pass']).$salt.md5($salt)); 
	}
	
	public function getUserById($id){
		return $this->users->fetchRow($this->users->getAdapter()->quoteInto('id=?', $id));
	}
	
	
	public function signOut(){
		$this->auth = Zend_Auth::getInstance()->clearIdentity();
	}
	
	public function deleteUser($id){
		$this->users->delete($this->users->getAdapter()->quoteInto('id=?', $id));
	}
	
	public function setAvatar($data,$id){
		$updateData = array(
			'avatar' => $data['image']
		);
		$this->users->update($updateData, $this->users->getAdapter()->quoteInto('id=?',$id));
		$storage = Zend_Auth::getInstance()->getStorage()->read();
		$storage->avatar = $data['image'];
		Zend_Auth::getInstance()->getStorage()->write($storage);
		
	}
	
	public function updateDayData($data){
		
	}
	
	public function deleteAvatar(){
		
	}
	
	public function listUsersQuery(){
		$query = $this->users->select();
		$query->from('users')->where('role <> "admin"')->order('id desc');
		return $query;
	}
	
	public function changeUserActivity($id){
		$user = $this->getUserById($id);
		$updateData = array();
		if($user['activity'] == 'y'){
			$updateData['activity'] = 'n';
		}else{
			$updateData['activity'] = 'y';
		}
		$this->users->update($updateData, $this->users->getAdapter()->quoteInto('id=?',$id));
		$result = array('errors' => array(),'activity' => $updateData['activity']);
		return $result;
	}
	
	public function searchUser($squery){
		$query =$this->users->getAdapter()->select();
		if(!empty($squery)){
			$query->from('users')
				->where('fullname LIKE \'%'.$squery.'%\' ')
				->orWhere('email LIKE \'%'.$squery.'%\' ');
		}else{
			$query->from('users');
		}
		//var_dump($query->assemble()); die;
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public function usersCount(){
		$query = $this->users->select()
		   ->from("users", array("num"=>"COUNT(*)"));
		$result = $this->users->fetchRow($query);
		return $result["num"];
	}
	
	public function getUsersByMask($mask){
		$query = $this->users->select();
		$query->from(array('u' => 'users'),array('id','fullname','email'))->where('fullname LIKE ? OR email LIKE ?','%'.$mask.'%');
		$result = $this->users->fetchAll($query)->toArray();
		//var_dump($result); die;
		return $result;
		//var_dump($query->assemble()); die;
	} 
}