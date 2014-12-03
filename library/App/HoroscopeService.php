<?php
class App_HoroscopeService {
	const HOROSCOPE_SIMPLE_TYPE_STRING = 'simple'; 
	const HOROSCOPE_TYPE_TYPE_STRING = 'type';
	const HOROSCOPE_TIME_TYPE_STRING = 'time';
	const HOROSCOPE_COMPABILITY_TYPE_STRING = 'compability';
	
	const HOROSCOPE_SIMPLE = 3;
	const HOROSCOPE_TYPE = 1;
	const HOROSCOPE_TIME = 2;
	const HOROSCOPE_COMPABILITY = 12;
	
	const HOROSCOPE_TYPE_TYPE = 1; 
	
	const HOROSCOPE_TYPE_PROF = 8;
	const HOROSCOPE_TYPE_HEALTH = 9;
	const HOROSCOPE_TYPE_CHILD = 10;
	const HOROSCOPE_TYPE_BUSINESS = 11;
	
	const HOROSCOPE_COMPABILITY_TYPE_LOVE = 1;
	const HOROSCOPE_COMPABILITY_TYPE_BUSINESS = 2;
	
	const HOROSCOPE_TIME_TODAY = 4;
	const HOROSCOPE_TIME_WEEK = 5;
	const HOROSCOPE_TIME_MONTH = 6;
	const HOROSCOPE_TIME_YEAR = 7;
	
	const HOROSCOPE_SIGN_TYPE_SUN = 1;
	const HOROSCOPE_SIGN_TYPE_KELT = 2;
	const HOROSCOPE_SIGN_TYPE_CHINA = 3;
	
	//const ETALON_YEAR = 2010;//?
	
	protected $horoscopeSign;
	protected $horoscopeByType;
	protected $horoscopeTypes;
	protected $horoscopeByTime;
	protected $horoscopeCompability;
	protected $horoscopeCompabilityTypes;
	protected $horoscopeCompabilityTypeAttributes;
	protected $horoscopeCompabilityTypeAttributeValue;
	protected $horoscopeKarma;
	protected $horoscopeSignType;
	protected $horoscopeSignChina;
	protected $horoscopeSignChinaType;
	protected $horoscopePages;
	
	protected $types;
	protected $sunSignsArray;
	protected $sunSignImages;  
	
	public function __construct(){
		$this->horoscopeSign = new Application_Model_DbTable_HoroscopeSignTable();
		$this->horoscopeByType = new Application_Model_DbTable_HoroscopeByTypeTable();
		$this->horoscopeTypes = new Application_Model_DbTable_HoroscopeTypesTable();
		$this->horoscopeByTime = new Application_Model_DbTable_HoroscopeByTimeTable();
		$this->horoscopeCompability = new Application_Model_DbTable_HoroscopeCompabilityTable();
		$this->horoscopeCompabilityTypes = new Application_Model_DbTable_HoroscopeCompabilityTypeTable();
		$this->horoscopeCompabilityTypeAttributes = new Application_Model_DbTable_HoroscopeCompabilityTypeAttributeTable();
		$this->horoscopeCompabilityTypeAttributeValue = new Application_Model_DbTable_HoroscopeCompabilityTypeAttributeValueTable();
		$this->horoscopeKarma = new Application_Model_DbTable_HoroscopeKarmaTable();
		$this->horoscopeSignType = new Application_Model_DbTable_HoroscopeSignTypeTable();
		$this->horoscopeSignChina = new Application_Model_DbTable_HoroscopeSignChinaTable();
		$this->horoscopeSignChinaType = new Application_Model_DbTable_HoroscopeSignChinaTypeTable();
		$this->horoscopePages = new Application_Model_DbTable_HoroscopePagesTable();
		
		$this->types = array(
				'today' => 'Гороскоп на сегодня и завтра',
				'business-compability' => 'Гороскоп совместимости',
				'love-compability' => 'Любовная совместимость',
				'simple' => 'Характеристика знака',
				'profession' => 'Гороскоп профессии',
				'karma' =>'Кармический гороскоп',
				'health' => 'Гороскоп здоровья',
				'child' => 'Гороскоп ребенка',
				'business' => 'Бизнес гороскоп',
				'week'=>'Гороскоп на неделю',
				'month' => 'Гороскоп на месяц',
				'year' => 'Гороскоп на год',
				'list' => '',
		);
		
		$this->sunSignsArray = array(
				'aries','taurus','gemini','cancer','leo','virgo',
				'libra','scorpio','sagittarius','capricorn',
				'aquarius','pisces'
		);
		//$this->sun
	}
	
	public function getSimpleSunSigns(){
		return $this->sunSignsArray;
	}
	public function getFrontendHoroscopeTypes(){
		//unset($this->types['list']);
		return $this->types;
	}
	public function getSunSigns(){
		$cache = Zend_Registry::get('cache');
		if(!$signs = $cache->load('sun_signs',true)){
			$signs = $this->horoscopeSign->fetchAll($this->horoscopeSign->getAdapter()->quoteInto('sign_type=?',self::HOROSCOPE_SIGN_TYPE_SUN))->toArray();
			$cache->save($signs,'sun_signs');
		}
		return $signs;
	}
	
	//public function getSun
	
	public function getKeltSigns(){
		return $this->horoscopeSign->fetchAll($this->horoscopeSign->getAdapter()->quoteInto('sign_type=?',self::HOROSCOPE_SIGN_TYPE_KELT))->toArray();
	}
	
	public function getChinaSigns(){
		return $this->horoscopeSign->fetchAll($this->horoscopeSign->getAdapter()->quoteInto('sign_type=?',self::HOROSCOPE_SIGN_TYPE_CHINA))->toArray();
	}
	
	public function getChinaTypes(){
		return $this->horoscopeSignChinaType->fetchAll()->toArray(); 
	}
	
	public function getSign($sign_type,$sign,$china_type = null){
		if(!$china_type && $sign_type != self::HOROSCOPE_SIGN_TYPE_CHINA){
			return $this->horoscopeSign->fetchRow($this->horoscopeSign->getAdapter()->quoteInto('id=?',$sign))->toArray();
		}else{
			$adapter = $this->horoscopeSignChina->getAdapter();
			$data = $this->horoscopeSign->fetchRow($adapter->quoteInto('id=?',$sign))->toArray();
			
			$adapter = $this->horoscopeSignChina->getAdapter();
			$query =  $adapter->select()->from(array('c'=>'horoscope_sign_china'))
						->joinLeft(array('s' => 'horoscope_sign'),'c.sign_id = s.id',array('sign','sign_ru'))
						->joinLeft(array('t' => 'horoscope_sign_china_type'),'c.china_sign_type_id = t.id',array('type','type_ru'))
						->where($adapter->quoteInto('s.id=?',$data['id']))
						->where($adapter->quoteInto('c.china_sign_type_id=?',$china_type));
			//var_dump($query->assemble()); die;
			$stm = $query->query();
			$horoscope = $stm->fetch();
			if(!$horoscope){
				$insertData = array(
					'description' => '',
					'china_sign_type_id' => $china_type,
					'sign_id' => $sign,
					'image' => ''
				);
				$insertData['id'] = $this->horoscopeSignChina->insert($insertData);
				return $insertData; 
			}
			return $horoscope;
		}
	}
	
	public function getSignByAlias($sign_type,$sign,$china_type = null){
		if(!$china_type && $sign_type != self::HOROSCOPE_SIGN_TYPE_CHINA){
			return $this->horoscopeSign->fetchRow($this->horoscopeSign->getAdapter()->quoteInto('sign=?',$sign))->toArray();
		}else{
			$adapter = $this->horoscopeSignChina->getAdapter();
			$data = $this->horoscopeSign->fetchRow($adapter->quoteInto('sign=?',$sign))->toArray();
			
			$query = $adapter->select()->from(array('c'=>'horoscope_sign_china'))
						->joinLeft(array('s' => 'horoscope_sign'),'c.sign_id = s.id',array('sign','sign_ru'))
						->joinLeft(array('t' => 'horoscope_sign_china_type'),'c.china_sign_type_id = t.id',array('type','type_ru'))
						->where($adapter->quoteInto('s.sign=?',$sign))
						->where($adapter->quoteInto('c.china_sign_type_id=?',$china_type));
			//var_dump($query->assemble()); die;
			$stm = $query->query();
			$horoscope = $stm->fetch();
			if(!$horoscope){
				$insertData = array(
					'description' => '',
					'china_sign_type_id' => $china_type,
					'sign_id' => $data['id'],
					'image' => ''
				);
				$insertData['id'] = $this->horoscopeSignChina->insert($insertData);
				return $insertData; 
			}
			return $horoscope;
		}
	}
	
	public function saveSign($sign,$signtype,$description,$chinaType = null){
		if($signtype == self::HOROSCOPE_SIGN_TYPE_SUN || $signtype == self::HOROSCOPE_SIGN_TYPE_KELT){
			$updateData = array(
					'description' => $description
			);
			$this->horoscopeSign->update($updateData, $this->horoscopeSign->getAdapter()->quoteInto('id=?',$sign));
		}
		if($signtype == self::HOROSCOPE_SIGN_TYPE_CHINA){
			$adapter = $this->horoscopeSignChina->getAdapter();
			$query =  $adapter->select()->from('horoscope_sign_china')
				->where($adapter->quoteInto('sign_id=?',$sign))
				->where($adapter->quoteInto('china_sign_type_id=?',$chinaType));
			//var_dump($query->assemble()); die;
			$stm = $query->query();
			$horoscope = $stm->fetch();
			//var_dump($horoscope); die;
			if(!$horoscope){
				$insertData = array(
						'description' => $description,
						'china_sign_type_id' => $chinaType,
						'sign_id' => $sign,
						'image' => ''
				);
				$insertData['id'] = $this->horoscopeSignChina->insert($insertData);
			}else{
				$updateData = array(
						'description' => $description,
				);
				$this->horoscopeSignChina->update($updateData, $adapter->quoteInto('id=?',$horoscope['id']));
			}
		}
	}
	
	public function getHoroscopeByTypeTypes(){
		return $this->horoscopeTypes->fetchAll('parent_id='.self::HOROSCOPE_TYPE_TYPE);
	}
	public function getHoroscopeByTimeTypes(){
		return $this->horoscopeTypes->fetchAll('parent_id='.self::HOROSCOPE_TIME_TYPE);
	}
	
	public function getHoroscopeByTypeAndSign($type,$sign){
		$query = $this->horoscopeByType->getAdapter()->select();
		$query->from('horoscope_by_type')
			->where($this->horoscopeByType->getAdapter()->quoteInto('type_id=?', $type))
			->where($this->horoscopeByType->getAdapter()->quoteInto('sign_id=?', $sign));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		if(!$horoscope){
			$insertData = array(
				'description' => '',
				'sign_id' => $sign,
				'type_id' => $type
			);
			$insertData['id'] = $this->horoscopeByType->insert($insertData);
			return $insertData;
		}
		return $horoscope;
	}
	
	public function getHoroscopeByTypeAndSignAlias($type,$sign){
		$data = $this->getSignByAlias(self::HOROSCOPE_SIGN_TYPE_SUN, $sign);
		$query = $this->horoscopeByType->getAdapter()->select();
		$query->from('horoscope_by_type')
		->where($this->horoscopeByType->getAdapter()->quoteInto('type_id=?', $type))
		->where($this->horoscopeByType->getAdapter()->quoteInto('sign_id=?', $data['id']));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($horoscope); die;
		if(!$horoscope){
			$insertData = array(
					'description' => '',
					'sign_id' => $data['id'],
					'type_id' => $type
			);
			$insertData['id'] = $this->horoscopeByType->insert($insertData);
			$insertData['sign'] = $data;
			return $insertData;
		}
		$horoscope['sign'] = $data;
		return $horoscope;
	}
	
	public function updateHoroscopeByTypeAndSign($type,$sign,$description){
		$query = $this->horoscopeByType->getAdapter()->select();
		$query->from('horoscope_by_type')
			->where($this->horoscopeByType->getAdapter()->quoteInto('type_id=?', $type))
			->where($this->horoscopeByType->getAdapter()->quoteInto('sign_id=?', $sign));
		//var_dump($query->assemble()); die;
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($horoscope); die;
		if(!$horoscope){
			$insertData = array(
					'description' => $description,
					'sign_id' => $sign,
					'type_id' => $type
			);
			$insertData['id'] = $this->horoscopeByType->insert($insertData);
			//return $insertData;
		}else{
			$updateData = array(
				'description' => $description,
			);
			$this->horoscopeByType->update($updateData,'id='.$horoscope['id']);
		}
	}
	
	public function getTimeHoroscopeItem($type,$startdate,$enddate,$sign){
		$data = array();
		switch($type){
			case 'today': $data = $this->getTodayData($startdate,$enddate,$sign); break;
			case 'week': $data = $this->getWeekData($startdate,$enddate,$sign); break;
			case 'month': $data = $this->getMonthData($startdate,$enddate,$sign); break;
			case 'year': $data = $this->getYearData($startdate,$enddate,$sign); break;
		}
		return $data;
	}
	
	public function getTodayData($startdate,$enddate,$sign){
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$sign));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
				'startdate' => $startdate,
				'enddate' => $enddate,
				'sign_id' => $sign,
				'description' => '',	
				'love_desc' => '',
				'business_desc' => '',
				'is_day' => 'y',
				'type_id' => self::HOROSCOPE_TIME_TODAY
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			return $insertData;
		}
		return $horoscope;
	}
	
	public function getTodayDataBySignAlias($startdate,$enddate,$sign){
		$data = $this->getSignByAlias(self::HOROSCOPE_SIGN_TYPE_SUN, $sign);
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')
			->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate>=?',$startdate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate<=?',$enddate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$data['id']))
			->order('startdate');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		//var_dump($query->assemble()); die;
		$horoscope = $stm->fetchAll();
		//var_dump($horoscope); die;
		if(!$horoscope){
			$result = array();
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $startdate,
					'sign_id' => $data['id'],
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'y',
					'type_id' => self::HOROSCOPE_TIME_TODAY
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			$insertData['sign'] = $data;
			
			$result[] = $insertData;
			
			$insertData = array(
					'startdate' => $enddate,
					'enddate' => $enddate,
					'sign_id' => $data['id'],
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'y',
					'type_id' => self::HOROSCOPE_TIME_TODAY
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			$insertData['sign'] = $data;
			
			$result[] = $insertData;
			
			return $result;
		}
		foreach($horoscope as $index => $value){
			$horoscope[$index]['sign'] = $data;
		}
		return $horoscope;
	}
	
	public function getWeekData($startdate,$enddate,$sign){
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$sign));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $sign,
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'n',
					'type_id' => self::HOROSCOPE_TIME_WEEK
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			return $insertData;
		}
		return $horoscope;
	}
	
	public function getWeekDataBySignAlias($startdate,$enddate,$sign){
		$data = $this->getSignByAlias(self::HOROSCOPE_SIGN_TYPE_SUN, $sign);
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')
			->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$data['id']));
			$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $data['id'],
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'n',
					'type_id' => self::HOROSCOPE_TIME_WEEK
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			$insertData['sign'] = $data;
			return $insertData;
		}
		$horoscope['sign'] = $data;
		return $horoscope;
	}
	
	public function getMonthData($startdate,$enddate,$sign){
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
		->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
		->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$sign));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $sign,
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'n',
					'type_id' => self::HOROSCOPE_TIME_MONTH
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			return $insertData;
		}
		return $horoscope;
	}
	
	public function getMonthDataBySignAlias($startdate,$enddate,$sign){
		$data = $this->getSignByAlias(self::HOROSCOPE_SIGN_TYPE_SUN, $sign);
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')
			->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$data['id']));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($horoscope); die;
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $data['id'],
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'n',
					'type_id' => self::HOROSCOPE_TIME_MONTH
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			$insertData['sign'] = $data;
			return $insertData;
		}
		$horoscope['sign'] = $data;
		return $horoscope;
	}
	
	public function getYearData($startdate,$enddate,$sign){
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$sign));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $sign,
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'n',
					'type_id' => self::HOROSCOPE_TIME_YEAR
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			return $insertData;
		}
		
		return $horoscope;
	}
	
	public function getYearDataBySignAlias($startdate,$enddate,$sign){
		$data = $this->getSignByAlias(self::HOROSCOPE_SIGN_TYPE_SUN, $sign);
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
			->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$data['id']));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $data['id'],
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'n',
					'type_id' => self::HOROSCOPE_TIME_YEAR
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			$insertData['sign'] = $data;
			return $insertData;
		}
		$horoscope['sign'] = $data;
		return $horoscope;
	}
	
	public function saveTimeHoroscopeItem($type,$startdate,$enddate,$sign,$description){
		$data = array();
		switch($type){
			case 'today': $data = $this->saveTodayData($startdate,$enddate,$sign,$description); break;
			case 'week': $data = $this->saveWeekData($startdate,$enddate,$sign,$description); break;
			case 'month': $data = $this->saveMonthData($startdate,$enddate,$sign,$description); break;
			case 'year': $data = $this->saveYearData($startdate,$enddate,$sign,$description); break;
		}
		return $data;
	}
	
	public function saveTodayData($startdate,$enddate,$sign,$description){
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
		->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
		->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$sign));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $sign,
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'y',
					'type_id' => self::HOROSCOPE_TIME_TODAY
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			//return $insertData;
		}else{
			$updateData = array(
				'description' => $description,
				'sign_id' => $sign,
				'startdate' => $startdate,
				'enddate' => $enddate,
				'love_desc' => '',
				'business_desc' => '',
			);
			$this->horoscopeByTime->update($updateData,'id='.$horoscope['id']);
		}
	}
	
	public function saveWeekData($startdate,$enddate,$sign,$description){
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
				->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
				->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$sign));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $sign,
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'y',
					'type_id' => self::HOROSCOPE_TIME_WEEK
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			//return $insertData;
		}else{
			$updateData = array(
					'description' => $description,
					'sign_id' => $sign,
					'startdate' => $startdate,
					'enddate' => $enddate,
					'love_desc' => '',
					'business_desc' => '',
			);
			$this->horoscopeByTime->update($updateData,'id='.$horoscope['id']);
		}
	}
	
	public function saveMonthData($startdate,$enddate,$sign,$description){
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
		->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
		->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$sign));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $sign,
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'y',
					'type_id' => self::HOROSCOPE_TIME_MONTH
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			//return $insertData;
		}else{
			$updateData = array(
					'description' => $description,
					'sign_id' => $sign,
					'startdate' => $startdate,
					'enddate' => $enddate,
					'love_desc' => '',
					'business_desc' => '',
			);
			$this->horoscopeByTime->update($updateData,'id='.$horoscope['id']);
		}
	}
	
	public function saveYearData($startdate,$enddate,$sign,$description){
		$query = $this->horoscopeByTime->getAdapter()->select();
		$query->from('horoscope_by_time')->where($this->horoscopeByTime->getAdapter()->quoteInto('startdate=?',$startdate))
				->where($this->horoscopeByTime->getAdapter()->quoteInto('enddate=?',$enddate))
				->where($this->horoscopeByTime->getAdapter()->quoteInto('sign_id=?',$sign));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		//var_dump($query->assemble()); die;
		if(!$horoscope){
			$insertData = array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'sign_id' => $sign,
					'description' => '',
					'love_desc' => '',
					'business_desc' => '',
					'is_day' => 'y',
					'type_id' => self::HOROSCOPE_TIME_YEAR
			);
			$insertData['id'] = $this->horoscopeByTime->insert($insertData);
			//return $insertData;
		}else{
			$updateData = array(
					'description' => $description,
					'sign_id' => $sign,
					'startdate' => $startdate,
					'enddate' => $enddate,
					'love_desc' => '',
					'business_desc' => '',
			);
			$this->horoscopeByTime->update($updateData,'id='.$horoscope['id']);
		}
	}
	
	public function getCompabilityTypes(){
		return $this->horoscopeCompabilityTypes->fetchAll()->toArray();
	}
	
	public function getCompabilityTypeAttributes($typeId){
		return $this->horoscopeCompabilityTypeAttributes
			->fetchAll($this->horoscopeCompabilityTypeAttributes->getAdapter()->quoteInto('compability_type_id=?',$typeId))->toArray();
	}
	
	public function getCompabilityItem($compId,$mainSign,$nestedSign/*,$mainGender,$nestedGender*/){
		$adapter = $this->horoscopeCompability->getAdapter();
		$query = $adapter->select();
		
		$mainSignQuoted1 = $adapter->quoteInto('mainsign_id=?',$mainSign);
		$nestedSignQuoted1 = $adapter->quoteInto('nestedsign_id=?',$nestedSign);
		
		$mainSignQuoted2 = $adapter->quoteInto('mainsign_id=?',$nestedSign);
		$nestedSignQuoted2 = $adapter->quoteInto('nestedsign_id=?',$mainSign);
		
		/*
		$mainGenderQuoted1 = $adapter->quoteInto('isman_mainsign=?',($mainGender=='man')?('y'):('n'));
		$nestedGenderQuoted1 = $adapter->quoteInto('isman_nestedsign=?',($nestedGender=='man')?('y'):('n'));
		
		$mainGenderQuoted2 = $adapter->quoteInto('isman_mainsign=?',($nestedGender == 'man')?('y'):('n'));
		$nestedGenderQuoted2 = $adapter->quoteInto('isman_nestedsign=?',($mainGender == 'man')?('y'):('n'));
		*/
		
		$compIdQuoted = $adapter->quoteInto('compability_type_id=?',$compId);
		$query->from('horoscope_compability')
				->where('('.$mainSignQuoted1.' AND '.$nestedSignQuoted1.' AND '.$compIdQuoted.')')
				->orWhere('('.$mainSignQuoted2.' AND '.$nestedSignQuoted2.' AND '.$compIdQuoted.')');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		
		//var_dump($horoscope); die;
		
		if(!$horoscope){
			$insertData = array(
					'mainsign_id' => $nestedSign,
					'nestedsign_id' => $mainSign,
					'compability_type_id' => $compId,
					'description' => '',
			);
			$insertData['id'] = $this->horoscopeCompability->insert($insertData);
			
			$attributes = $this->horoscopeCompabilityTypeAttributes->fetchAll($adapter->quoteInto('compability_type_id=?',$compId))->toArray();
			$insertData['attributes'] = $attributes;
			$insertData['attribute_values'] = array();
			foreach($attributes as &$attribute){
				$attributeValue = array(
						'compability_type_id' => $compId,
						'compability_id' => $insertData['id'],
						'compability_attribute_id'=> $attribute['id'],
						'value' => ''
				);
				$attributeValue['id'] = $this->horoscopeCompabilityTypeAttributeValue->insert($attributeValue);
				$insertData['attribute_values'][] = $attributeValue;
			}
			
			$insertData = array(
					'mainsign_id' => $mainSign,
					'nestedsign_id' => $nestedSign,
					'compability_type_id' => $compId,
					'description' => '',
			);
			$insertData['id'] = $this->horoscopeCompability->insert($insertData);
			
			$attributes = $this->horoscopeCompabilityTypeAttributes->fetchAll($adapter->quoteInto('compability_type_id=?',$compId))->toArray();
			$insertData['attributes'] = $attributes;
			$insertData['attribute_values'] = array();
			foreach($attributes as &$attribute){
				$attributeValue = array(
						'compability_type_id' => $compId,
						'compability_id' => $insertData['id'],
						'compability_attribute_id'=> $attribute['id'],
						'value' => ''
				);
				$attributeValue['id'] = $this->horoscopeCompabilityTypeAttributeValue->insert($attributeValue);
				$insertData['attribute_values'][] = $attributeValue;
			}
			return $insertData;
		}
		$horoscope['attributes'] = $this->horoscopeCompabilityTypeAttributes->fetchAll($adapter->quoteInto('compability_type_id=?',$compId))->toArray();
		$horoscope['attribute_values'] =  $this->horoscopeCompabilityTypeAttributeValue->fetchAll($adapter->quoteInto('compability_id=?',$horoscope['id']))->toArray();
		/*
		if($mainSign == $horoscope['nestedsign_id'] && $horoscope['nestedsign_id'] != $horoscope['mainsign_id']){
			$tmp = $horoscope['mainsign_id'];
			$horoscope['mainsign_id'] = $horoscope['nestedsign_id'];
			$horoscope['nestedsign_id'] = $tmp;
		}
		*/
		return $horoscope;
	}
	
	public function saveCompabilityItem($compability, $mainsign, $nestedsign, /*$maingender, $nestedgender,*/$description,$attributes){
		//var_dump($attributes); die;
		$adapter = $this->horoscopeCompability->getAdapter();
		$query = $adapter->select();
		$mainSignQuoted1 = $adapter->quoteInto('mainsign_id=?',$mainsign);
		$nestedSignQuoted1 = $adapter->quoteInto('nestedsign_id=?',$nestedsign);
		
		$mainSignQuoted2 = $adapter->quoteInto('mainsign_id=?',$nestedsign);
		$nestedSignQuoted2 = $adapter->quoteInto('nestedsign_id=?',$mainsign);
		
		/*
		$mainGenderQuoted1 = $adapter->quoteInto('isman_mainsign=?',($maingender=='man')?('y'):('n'));
		$nestedGenderQuoted1 = $adapter->quoteInto('isman_nestedsign=?',($nestedgender=='man')?('y'):('n'));
		
		$mainGenderQuoted2 = $adapter->quoteInto('isman_mainsign=?',($nestedgender == 'man')?('y'):('n'));
		$nestedGenderQuoted2 = $adapter->quoteInto('isman_nestedsign=?',($maingender == 'man')?('y'):('n'));
		*/
		
		/*
		$query->from('horoscope_compability')->where($adapter->quoteInto('compability_type_id=?',$compability))
				//->where('('.$mainSignQuoted1.' AND '.$nestedSignQuoted1.') OR ('.$mainSignQuoted2.' AND '.$nestedSignQuoted2.')')
				//->where('('.$mainGenderQuoted1.' AND '.$nestedGenderQuoted1.') OR ('.$mainGenderQuoted2.' AND '.$nestedGenderQuoted2.')');
				->where('('.$mainSignQuoted1.' AND '.$nestedSignQuoted1.') AND ('.$mainGenderQuoted1.' AND '.$nestedGenderQuoted1.')')
				->orWhere('('.$mainSignQuoted2.' AND '.$nestedSignQuoted2.') AND ('.$mainGenderQuoted2.' AND '.$nestedGenderQuoted2.')');
		*/
				
		//var_dump($query->assemble()); die;
		$compIdQuoted = $adapter->quoteInto('compability_type_id=?',$compability);
		$query->from('horoscope_compability')
				->where('('.$mainSignQuoted1.' AND '.$nestedSignQuoted1.' AND '.$compIdQuoted.')')
				->orWhere('('.$mainSignQuoted2.' AND '.$nestedSignQuoted2.' AND '.$compIdQuoted.')');
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$horoscope = $stm->fetch();
		
		if(!$horoscope){
			$insertData = array(
					'mainsign_id' => $nestedsign,
					'nestedsign_id' => $mainsign,
					'compability_type_id' => $compability,
					'description' => $description,
			);
			$insertData['id'] = $this->horoscopeCompability->insert($insertData);
			foreach($attributes as &$attribute){
				$attributeValue = array(
						'compability_type_id' => $compability,
						'compability_id' => $insertData['id'],
						'compability_attribute_id'=> $attribute['id'],
						'value' => $attribute['value']
				);
				$attributeValue['id'] = $this->horoscopeCompabilityTypeAttributeValue->insert($attributeValue);
			}
			
			$insertData = array(
					'mainsign_id' => $mainsign,
					'nestedsign_id' => $nestedsign,
					'compability_type_id' => $compability,
					'description' => $description,
			);
			$insertData['id'] = $this->horoscopeCompability->insert($insertData);
			foreach($attributes as &$attribute){
				$attributeValue = array(
						'compability_type_id' => $compability,
						'compability_id' => $insertData['id'],
						'compability_attribute_id'=> $attribute['id'],
						'value' => $attribute['value']
				);
				$attributeValue['id'] = $this->horoscopeCompabilityTypeAttributeValue->insert($attributeValue);
			}
		}else{
			$stm = $query->query();
			$lines = $stm->fetchAll();
			foreach($lines as $item){
				$updateData = array(
						'description' => $description
				);
				$this->horoscopeCompability->update($updateData,$adapter->quoteInto('id=?',$item['id']));
				$attributeValues = $this->horoscopeCompabilityTypeAttributeValue->fetchAll($adapter->quoteInto('compability_id=?',$item['id']));
				foreach($attributeValues as $value){
					foreach($attributes as $attribute){
						if($attribute['id'] == $value['compability_attribute_id']){
							$attributeValue = array(
									'value' => $attribute['value']
							);
							$this->horoscopeCompabilityTypeAttributeValue->update($attributeValue,$adapter->quoteInto('id=?',$value['id']));
						}
					}
				}
			}
		}
	}
	
	public function getKarmaQuery(){
		$adapter = $this->horoscopeKarma->getAdapter();
		$query = $adapter->select();
		$query->from(array('k'=>'horoscope_karma'))
			->joinLeft(array('s' => 'horoscope_sign'),'k.sign_id = s.id',array('sign','sign_ru'))
			->order('enddate DESC');
		return $query;
	}
	
	public function addKarmaPeriod($data){
		$insertData = array(
			'startdate' 	=> $data['startdate'],
			'enddate' 		=> $data['enddate'],
			'is_retrograd' => $data['is_retrograd'],
			'sign_id' 		=> $data['sign'],
			'description' 	=> $data['desc']
		);
		$this->horoscopeKarma->insert($insertData);
	}
	
	public function getLastKarmaPeriod(){
		$adapter = $this->horoscopeKarma->getAdapter();
		$query = $adapter->select();
		$query->from(array('k'=>'horoscope_karma'))
			->order('enddate DESC')->limit(1);
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$period = $stm->fetch();
		if(!$period){
			$period = array(
				'enddate' => '1940-01-01'
			);
		} 
		return $period;
	}
	
	public function getKarmaPeriodById($id){
		$adapter = $this->horoscopeKarma->getAdapter();
		$query = $adapter->select()->from(array('k'=>'horoscope_karma'))
			->joinLeft(array('s' => 'horoscope_sign'),'k.sign_id = s.id',array('sign','sign_ru','image'))
			->where($adapter->quoteInto('k.id=?',$id));
		//var_dump($query->assemble()); die;
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetch();
		//return $this->horoscopeKarma->fetchRow($this->horoscopeKarma->getAdapter()->quoteInto('id=?',$id))->toArray();
	} 
	
	public function updateKarmaPeriod($data,$id){
		$updateData = array(
			'startdate' => $data['startdate'],
			'enddate' => $data['enddate'],
			'is_retrograd' => $data['is_retrograd'],
			'sign_id' => $data['sign'],
			'description' => $data['desc']
		);
		$this->horoscopeKarma->update($updateData,$this->horoscopeKarma->getAdapter()->quoteInto('id=?',$id));
	}
	
	public function removeKarmaPeriod($id){
		$this->horoscopeKarma->delete($this->horoscopeKarma->getAdapter()->quoteInto('id=?',$id));
	}
	
	public function getSignTypes(){
		return $this->horoscopeSignType->fetchAll()->toArray();
	}
	
	public function getKarmaByBirthday($birthday){
		$adapter = $this->horoscopeKarma->getAdapter();
		$query = $adapter->select()->from(array('k'=>'horoscope_karma'))
			->joinLeft(array('s' => 'horoscope_sign'),'k.sign_id = s.id',array('sign','sign_ru'))
			->where($adapter->quoteInto(new Zend_Db_Expr('DATE(k.startdate)').'<=?',$birthday))
			->where($adapter->quoteInto(new Zend_Db_Expr('DATE(k.enddate)').'>=?',$birthday));
		//var_dump($query->assemble()); die;
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetch();
	}
	
	function russianMonth($month){
		switch ($month){
			case 1: $m = 'январь'; break;
			case 2: $m = 'февраль'; break;
			case 3: $m = 'март'; break;
			case 4: $m = 'апрель'; break;
			case 5: $m = 'май'; break;
			case 6: $m = 'июнь'; break;
			case 7: $m = 'июль'; break;
			case 8: $m = 'август'; break;
			case 9: $m = 'сентябрь'; break;
			case 10: $m = 'октябрь'; break;
			case 11: $m = 'ноябрь'; break;
			case 12: $m = 'декабрь'; break;
		}
		return $m;
	}
	
	public function getSunSignByBirthday($birthday){
		/*
		$date = new Zend_Date($birthday);
		$month = $date->get(Zend_Date::MONTH);
		$day = $date->get(Zend_Date::DAY);
		$alias = '';
		if( ($month == 3  && $day >= 21) || ($month == 4  && $day <= 20 ) ){
			$alias = 'aries';
		}
		if( ($month == 4  && $day >= 21) || ($month == 5  && $day <= 20 ) ){
			$alias = 'taurus';
		}
		if( ($month == 5  && $day >= 21) || ($month == 6  && $day <= 21 ) ){
			$alias = 'gemini';
		}
		if( ($month == 6  && $day >= 22) || ($month == 7  && $day <= 22 ) ){
			$alias = 'cancer';
		}
		if( ($month == 7  && $day >= 23) || ($month == 8  && $day <= 23 ) ){
			$alias = 'leo';
		}
		if( ($month == 8  && $day >= 24) || ($month == 9  && $day <= 23 ) ){
			$alias = 'virgo';
		}
		if( ($month == 9  && $day >= 24) || ($month == 10  && $day <= 23 ) ){
			$alias = 'libra';
		}
		if( ($month == 10  && $day >= 24) || ($month == 11  && $day <= 22 ) ){
			$alias = 'scorpio';
		}
		if( ($month == 11  && $day >= 23) || ($month == 12  && $day <= 21 ) ){
			$alias = 'sagittarius';
		}
		if( ($month == 12  && $day >= 22) || ($month == 1  && $day <= 20 ) ){
			$alias = 'capricorn';
		}
		if( ($month == 1  && $day >= 21) || ($month == 2  && $day <= 20 ) ){
			$alias = 'aquarius';
		}
		if( ($month == 2  && $day >= 21) || ($month == 3  && $day <= 20 ) ){
			$alias = 'pisces';
		}
		return $alias;
		*/
		$year = '2010';
		$birthday = $year.'-'.date('m-d',strtotime($birthday));
		
		$adapter = $this->horoscopeSign->getAdapter();
		$query = $adapter->select()->from(array('h'=>'horoscope_sign'))
			->where($adapter->quoteInto(new Zend_Db_Expr('DATE(h.startdate)').'<=?',$birthday))
			->where($adapter->quoteInto(new Zend_Db_Expr('DATE(h.enddate)').'>=?',$birthday));
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetch();
	}
	
	public function getShortSunSignsToday(){
		$today = date('Y-m-d');
		$adapter = $this->horoscopeByTime->getAdapter();
		$signIds = array(1,2,3,4,5,6,7,8,9,10,11,12);
		$query = $adapter->select()->from(array('k'=>'horoscope_by_time'))
		->joinLeft(array('s' => 'horoscope_sign'),'k.sign_id = s.id',array('sign','sign_ru','sign_startdate','sign_enddate'))
		->where($adapter->quoteInto('k.sign_id in (?)',$signIds))
		->where($adapter->quoteInto('k.startdate=?',$today))
		->where($adapter->quoteInto('k.enddate=?',$today))
		->order('k.sign_id ASC');
		//var_dump($query->assemble()); die;
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
	
	public static function imageBySign($sign){
		switch($sign){
			case 'aries': return 'sign1.png';
			case 'taurus': return 'sign2.png';
			case 'gemini': return 'sign3.png';
			case 'cancer': return 'sign4.png';
			case 'leo': return 'sign5.png';
			case 'virgo': return 'sign6.png';
			case 'libra': return 'sign7.png';
			case 'scorpio': return 'sign8.png';
			case 'sagittarius': return 'sign9.png';
			case 'capricorn': return 'sign10.png';
			case 'aquarius': return 'sign11.png';
			case 'pisces': return 'sign12.png';
		}
	}
	
	public function listPagesQuery(){
		$query = $this->horoscopePages->select();
		$query->from('horoscope_pages')->order('id desc');
		return $query;
	}
	
	public function addPage($data){
		$insertData = array(
			'horoscope_type' => $data['page_type'],
			'name_ru' => $data['name_ru'],
			'title' => $data['title'],
			'keywords' => $data['seokeywords'],
			'description' => $data['seodescription'],
			'minidesc' => $data['minidesc'],
		);
		$this->horoscopePages->insert($insertData);
		$cache = Zend_Registry::get('cache');
		$cache->remove('horoscope_pages');
	}
	
	public function savePage($data,$id){
		$updateData = array(
			'horoscope_type' => $data['page_type'],
			'name_ru' => $data['name_ru'],
			'title' => $data['title'],
			'keywords' => $data['seokeywords'],
			'description' => $data['seodescription'],
			'minidesc' => $data['minidesc'],
		);
		//var_dump($updateData); die;
		$this->horoscopePages->update($updateData,$this->horoscopePages->getAdapter()->quoteInto('id=?', $id));
		$cache = Zend_Registry::get('cache');
		$cache->remove('horoscope_pages');
	}
	
	public function deletePage($id){
		$this->horoscopePages->delete($this->horoscopePages->getAdapter()->quoteInto('id=?', $id));
		$cache = Zend_Registry::get('cache');
		$cache->remove('horoscope_pages');
	}
	
	public function getPageById($id){
		return $this->horoscopePages->fetchRow($this->horoscopePages->getAdapter()->quoteInto('id=?', $id))->toArray();
	}
	
	public function getAllPages(){
		$cache = Zend_Registry::get('cache');
		$pages = array();
		if(!$pages = $cache->load('horoscope_pages',true)){
			$pages = $this->horoscopePages->fetchAll(true)->toArray();
			$cache->save($pages,'horoscope_pages');
		}
		return $pages;
	}
	
	public function searchPage($squery){
		$query =$this->horoscopePages->getAdapter()->select();
		if(!empty($squery)){
			$query->from('horoscope_pages')
				->where('name_ru LIKE \'%'.$squery.'%\' ');
		}else{
			$query->from('horoscope_pages');
		}
		$stm = $query->query();
		$stm->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stm->fetchAll();
	}
}