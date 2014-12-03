<?php
class App_MoonService {
	
	protected $moonDay;
	protected $moonDayAttribute;
	protected $moonDayAttributeValue; 
	protected $moonCalendar;
	protected $moonCalendarInSign;
	protected $moonDaysCalendar;
	protected $moonPhase;
	protected $moonInSign;
	
	public function __construct(){
		$this->moonDay = new Application_Model_DbTable_MoonDayTable();
		$this->moonDayAttribute = new Application_Model_DbTable_MoonDayAttributeTable();
		$this->moonDayAttributeValue = new Application_Model_DbTable_MoonDayAttributeValueTable();
		$this->moonCalendar = new Application_Model_DbTable_MoonCalendarTable();
		$this->moonCalendarInSign = new Application_Model_DbTable_MoonCalendarInSignTable();
		$this->moonDaysCalendar = new Application_Model_DbTable_MoonDayCalendarTable();
		$this->moonPhase = new Application_Model_DbTable_MoonPhaseTable();
		$this->moonInSign = new Application_Model_DbTable_MoonInSignTable();
	}
	
	public function buildDaysQuery(){
		$query = $this->moonDay->getAdapter()->select();
		return $query->from('moon_day')->order('day_number DESC');
	}
	
	public function addMoonDay($data){
		//var_dump($data); die;
		$insertData = array(
				'day_number' => $data['number'],
				'description' => $data['desc'],
				'moon_phase_id' => $data['phase'],
				'image' => $data['image']
		);
		$this->moonDay->insert($insertData);
	}
	public function saveMoonDay($data,$id){
		//var_dump($data); die;
		$updateData = array(
				'day_number' => $data['number'],
				'description' => $data['desc'],
				'moon_phase_id' => $data['phase'],
				
		);
		if(isset($data['image'])){
			$updateData['image'] = $data['image'];
		}
		$this->moonDay->update($updateData,$this->moonDay->getAdapter()->quoteInto('id=?',$id));
	}
	public function getDayById($id){
		return $this->moonDay->fetchRow($this->moonDay->getAdapter()->quoteInto('id=?',$id))->toArray();
	}
	public function removeDay($id){
		$this->moonDay->delete($this->moonDay->getAdapter()->quoteInto('id=?',$id));
	}
	public function getAllMoonDays(){
		return $this->moonDay->fetchAll()->toArray();
	}
	
	public function getAllDayAttributes(){
		return $this->moonDayAttribute->fetchAll()->toArray();
	}
	
	public function getDayAttribute($day,$attribute){
		$adapter = $this->moonDay->getAdapter();
		$dayData = $this->moonDay->fetchRow($adapter->quoteInto('id=?', $day))->toArray();
		//var_dump($dayData); die;
		$query = $adapter->select();
		$query->from('moon_day_attribute_value')
			->where($adapter->quoteInto('moon_day_id=?', $dayData['id']))
			->where($adapter->quoteInto('attribute_id=?', $attribute));
		//var_dump($query->assemble()); die;
		$stm = $query->query();
		$data = $stm->fetch();
		if(empty($data)){
			$insertData = array(
				'description' 	=> '',
				'rating'		=> '0',
				'attribute_id' 	=> $attribute,
				'moon_day_id'	=> $dayData['id']		
			);
			$insertData['id'] = $this->moonDayAttributeValue->insert($insertData);
			return $insertData;
		}
		return $data;
	}
	
	public function saveDayAttribute($day,$attribute,$rating,$description){
		$attribute = $this->getDayAttribute($day, $attribute);
		$attribute['description'] = $description;
		$attribute['rating'] = $rating;
		$this->moonDayAttributeValue->update($attribute, 'id='.$attribute['id']);
	}
	
	public function getMoonSign($sign){
		$adapter = $this->moonInSign->getAdapter();
		$query = $adapter->select();
		$query->from('moon_in_sign')
			->where($adapter->quoteInto('sign_id=?', $sign));
		$stm = $query->query();
		$data = $stm->fetch();
		if(empty($data)){
			$insertData = array(
					'description' 	=> '',
					'sign_id'		=> $sign,
			);
			$insertData['id'] = $this->moonInSign->insert($insertData);
			return $insertData;
		}
		return $data;
	}
	
	public function getAssosiateDay($date){
		//echo '!!!!!!'; die;
		$adapter = $this->moonDaysCalendar->getAdapter();
		/*
		$query = $this->moonDaysCalendar->select();
		$query->setIntegrityCheck(false);
		$query->from(array('m'=>'moon_calendar'))
			->joinLeft(array('d' => 'moon_day_calendar'), 'd.moon_calendar_id = m.id',array())
			->where($adapter->quoteInto('m.day_date=?',$date));
		//var_dump($query->assemble()); die;
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$data = $stm->fetch();
		*/
		$day = $this->moonCalendar->fetchRow($adapter->quoteInto('day_date=?',$date));
		if(empty($day)){
			$insertData = array(
				'day_date' => $date,
			);
			$insertData['id'] = $this->moonCalendar->insert($insertData);
			$insertData['moonDays'][] = array('moon_day_id' => null,'day_start' => null,'moon_calendar_id' => $insertData['id'],'day_number' => null);
			return $insertData;
		}
		$day = $day->toArray();
		$moonDays = $this->moonDaysCalendar->fetchAll($adapter->quoteInto('moon_calendar_id=?',$day['id']));
		if(!$moonDays){
			$day['moonDays'][] = array('moon_day_id' => null,'day_start' => null,'moon_calendar_id' => $day['id'],'day_number' => null); 
		}else{
			//$day['moonDays'] = $moonDays->toArray();
			$moonDays = $moonDays->toArray();
			foreach($moonDays as $index => $moonDay){
				$data = $this->moonDay->fetchRow($adapter->quoteInto('id=?',$moonDay['moon_day_id']))->toArray();
				$moonDays[$index]['day_number'] = $data['day_number']; 
			}
			$day['moonDays'] = $moonDays;
		}
		return $day;
	}
	
	public function getDateSmallData($date){
		$day = array();
		$adapter = $this->moonCalendar->getAdapter();
		$day = $this->moonCalendar->fetchRow($adapter->quoteInto('day_date=?',$date));
		
		if(!$day){
			return array();
		}
		$day = $day->toArray();
		$query = $this->moonCalendarInSign->select();
		$query->setIntegrityCheck(false);
		$query->from(array('mcs' => 'moon_calendar_in_sign'))
			->joinLeft(array('ms' => 'moon_in_sign'), 'ms.id = mcs.moon_in_sign_id',array('sign_id','description'))
			->joinLeft(array('hs' => 'horoscope_sign'), 'hs.id = ms.sign_id',array('sign_ru'))
			->where($adapter->quoteInto('mcs.moon_calendar_id=?',$day['id']));
		//var_dump($query->assemble()); die;
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$day['in_signs'] = $stm->fetchAll();
		
		$attributes = array(1,14,21,16,17);
		
		$moonDays = $this->moonDaysCalendar->fetchAll($adapter->quoteInto('moon_calendar_id=?',$day['id']));
		
		if(!$moonDays){
			$day['moonDays'] = null;
		}else{
			$moonDays = $moonDays->toArray();
			
			foreach($moonDays as $index => $moonDay){
				$query = $this->moonDay->select();
				$query->setIntegrityCheck(false);
				$query->from(array('md'=>'moon_day'))
				->joinLeft(array('mp' => 'moon_phase'), 'mp.id = md.moon_phase_id',array('phase','phase_desc'=>'description','short_desc'))
				->where($adapter->quoteInto('md.id=?', $moonDay['moon_day_id']));
				$stm = $query->query(Zend_Db::FETCH_ASSOC);
				$moonDays[$index]['day_detail'] = $stm->fetch();
		
				$query = $this->moonDay->select();
				$query->setIntegrityCheck(false);
				$query->from(array('av'=>'moon_day_attribute_value'))
				->joinLeft(array('a' => 'moon_day_attribute'), 'a.id = av.attribute_id',array('name'))
				->where($adapter->quoteInto('av.attribute_id in (?)',$attributes))
				->where($adapter->quoteInto('moon_day_id=?',$moonDay['moon_day_id']))
				->order('av.attribute_id');
				
				$stm = $query->query(Zend_Db::FETCH_ASSOC);
				$item = $stm->fetchAll();
				$moonDays[$index]['attributes'] = $item;
			}
			$day['moonDays'] = $moonDays;
		}
		if(count($day['moonDays'])){
			foreach($day['moonDays'] as $dayindex => $moonday){
				foreach ($moonday['attributes'] as $index => $attribute){
					$day['moonDays'][$dayindex]['attributes'][$index]['view_rating'] = App_UtilsService::ratingToStars($attribute['rating']);
				}
			}
		}
		/*
		echo '<pre>'; 
		var_dump($day); die;
		*/
		return $day;
	}
	
	public function getDateDetailData($date){
		$day = array();
		$adapter = $this->moonCalendar->getAdapter();
		$day = $this->moonCalendar->fetchRow($adapter->quoteInto('day_date=?',$date));
		if(!$day){
			return array();
		}
		$day = $day->toArray();
		$query = $this->moonCalendarInSign->select();
		$query->setIntegrityCheck(false);
		$query->from(array('mcs' => 'moon_calendar_in_sign'))
			->joinLeft(array('ms' => 'moon_in_sign'), 'ms.id = mcs.moon_in_sign_id',array('sign_id','description'))
			->joinLeft(array('hs' => 'horoscope_sign'), 'hs.id = ms.sign_id',array('sign_ru'))
			->where($adapter->quoteInto('mcs.moon_calendar_id=?',$day['id']));
		//var_dump($query->assemble()); die;
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$day['in_signs'] = $stm->fetchAll();
		
		$moonDays = $this->moonDaysCalendar->fetchAll($adapter->quoteInto('moon_calendar_id=?',$day['id']));
		
		if(!$moonDays){
			$day['moonDays'] = null;
		}else{
			$moonDays = $moonDays->toArray();
			foreach($moonDays as $index => $moonDay){
				$query = $this->moonDay->select();
				$query->setIntegrityCheck(false);
				$query->from(array('md'=>'moon_day'))
				->joinLeft(array('mp' => 'moon_phase'), 'mp.id = md.moon_phase_id',array('phase','phase_desc'=>'description','short_desc'))
				->where($adapter->quoteInto('md.id=?', $moonDay['moon_day_id']));
				$stm = $query->query(Zend_Db::FETCH_ASSOC);
				$moonDays[$index]['day_detail'] = $stm->fetch();
			
				$query = $this->moonDay->select();
				$query->setIntegrityCheck(false);
				$query->from(array('av'=>'moon_day_attribute_value'))
				->joinLeft(array('a' => 'moon_day_attribute'), 'a.id = av.attribute_id',array('name'))
				->where($adapter->quoteInto('moon_day_id=?',$moonDay['moon_day_id']))
				->order('av.attribute_id');
				$stm = $query->query(Zend_Db::FETCH_ASSOC);
				$item = $stm->fetchAll();
				$moonDays[$index]['attributes'] = $item;
			}
			$day['moonDays'] = $moonDays;
		}
		if(count($day['moonDays'])){
			foreach($day['moonDays'] as $dayindex => $moonday){
				foreach ($moonday['attributes'] as $index => $attribute){
					$day['moonDays'][$dayindex]['attributes'][$index]['view_rating'] = App_UtilsService::ratingToStars($attribute['rating']);
				}
			}
		}
		/*
		echo '<pre>';
		var_dump($day); die;
		*/
		return $day;
	}
	
	public function saveAssociate($date,$newday){
		$adapter = $this->moonDaysCalendar->getAdapter();
		$day = $this->moonCalendar->fetchRow($adapter->quoteInto('day_date=?',$date))->toArray();
		if(empty($day)){
			$insertData = array(
				'day_date' => $date,
			);
			$id = $this->moonCalendar->insert($insertData);
			if(count($newday['moonDays'])){
				foreach($newday['moonDays'] as $moonDay){
					if(isset($moonDay['moon_day_id']) && null !== $moonDay['moon_day_id'] ){
						$insertData = array(
							'moon_day_id' => $moonDay['moon_day_id'],
							'moon_calendar_id' => $id	
						);
						if(!empty($moonDay['day_start'])){
							$insertData['day_start'] = $moonDay['day_start'];
						}
						$this->moonDaysCalendar->insert($insertData);
					}
				}
			}
		}else{
			$this->moonDaysCalendar->delete($adapter->quoteInto('moon_calendar_id=?', $day['id']));
			if(count($newday['moonDays'])){
				foreach($newday['moonDays'] as $moonDay){
					if(isset($moonDay['moon_day_id']) && null !== $moonDay['moon_day_id'] ){
						$insertData = array(
								'moon_day_id' => $moonDay['moon_day_id'],
								'moon_calendar_id' => $day['id']
						);
						if(!empty($moonDay['day_start'])){
							$insertData['day_start'] = $moonDay['day_start'];
						}
						$this->moonDaysCalendar->insert($insertData);
					}
				}
			}
		}
	}
	
	public function saveSignAssociate($date,$newsign){
		$adapter = $this->moonDaysCalendar->getAdapter();
		$day = $this->moonCalendar->fetchRow($adapter->quoteInto('day_date=?',$date))->toArray();
		if(empty($day)){
			$insertData = array(
					'day_date' => $date,
			);
			$id = $this->moonCalendar->insert($insertData);
			if(count($newsign['moonDays'])){
				foreach($newsign['moonDays'] as $moonDay){
					if(isset($moonDay['moon_in_sign_id']) && null !== $moonDay['moon_in_sign_id'] ){
						$insertData = array(
								'moon_in_sign_id' => $moonDay['moon_in_sign_id'],
								'moon_calendar_id' => $id
						);
						if(!empty($moonDay['signstart'])){
							$insertData['signstart'] = $moonDay['signstart'];
						}
						$this->moonCalendarInSign->insert($insertData);
					}
				}
			}
		}else{
			$this->moonCalendarInSign->delete($adapter->quoteInto('moon_calendar_id=?', $day['id']));
			if(count($newsign['moonDays'])){
				foreach($newsign['moonDays'] as $moonDay){
					if(isset($moonDay['moon_in_sign_id']) && null !== $moonDay['moon_in_sign_id'] ){
						$insertData = array(
								'moon_in_sign_id' => $moonDay['moon_in_sign_id'],
								'moon_calendar_id' => $day['id']
						);
						if(!empty($moonDay['signstart'])){
							$insertData['signstart'] = $moonDay['signstart'];
						}
						$this->moonCalendarInSign->insert($insertData);
					}
				}
				
			}
		}
	}
	
	public function getAssosiateSign($date){
		$adapter = $this->moonDaysCalendar->getAdapter();
		$day = $this->moonCalendar->fetchRow($adapter->quoteInto('day_date=?',$date));
		if(empty($day)){
			$insertData = array(
					'day_date' => $date,
			);
			$insertData['id'] = $this->moonCalendar->insert($insertData);
			$insertData['moonDays'][] = array('moon_in_sign_id' => null,'day_start' => null,'moon_calendar_id' => $insertData['id'],'day_number' => null);
			return $insertData;
		}
		$day = $day->toArray();
		$moonDays = $this->moonCalendarInSign->fetchAll($adapter->quoteInto('moon_calendar_id=?',$day['id']));
		if(!$moonDays){
			$day['moonDays'][] = array('moon_in_sign_id' => null,'day_start' => null,'moon_calendar_id' => $day['id'],'day_number' => null);
		}else{
			$moonDays = $moonDays->toArray();
			foreach($moonDays as $index => $moonDay){
				$query = $this->moonInSign->select();
				$query->setIntegrityCheck(false);
				$query->from(array('ms'=>'moon_in_sign'))
				->joinLeft(array('hs' => 'horoscope_sign'), 'hs.id = ms.sign_id',array('sign_ru'))
				->where($adapter->quoteInto('ms.id=?',$moonDay['moon_in_sign_id']));
				$stm = $query->query(Zend_Db::FETCH_ASSOC);
				$data = $stm->fetch();
				//var_dump($data); die;
				$moonDays[$index]['day_number'] = $data['sign_ru'];
			}
			$day['moonDays'] = $moonDays;
		}
		return $day;
	}
	
	public function saveMoonSign($sign,$description){
		$adapter = $this->moonInSign->getAdapter();
		$data = $this->getMoonSign($sign);
		$data['description'] = $description;
		
		$this->moonInSign->update($data, $adapter->quoteInto('id=?', $data['id']));
	}
	
	public function buildPhaseQuery(){
		$query = $this->moonPhase->getAdapter()->select();
		return $query->from('moon_phase')->order('id DESC');
	}
	
	public function addPhase($data){
		$insertData = array(
			'phase' => $data['name'],
			'short_desc' => $data['short_desc'],
			'description' => $data['desc']	
		);
		$this->moonPhase->insert($insertData);
	}
	public function getPhaseById($id){
		return $this->moonPhase->fetchRow($this->moonPhase->getAdapter()->quoteInto('id=?',$id))->toArray();
	}
	public function updatePhase($data,$id){
		$updateData = array(
				'phase' => $data['name'],
				'short_desc' => $data['short_desc'],
				'description' => $data['desc'],
		);
		$this->moonPhase->update($updateData, $this->moonPhase->getAdapter()->quoteInto('id=?',$id));
	}
	public function removePhase($id){
		$this->moonPhase->delete($this->moonPhase->getAdapter()->quoteInto('id=?',$id));
	}
	
	public function getAllPhases(){
		return $this->moonPhase->fetchAll()->toArray();
	}
	
	public function getDays($monthYear){
		$date = explode('-',$monthYear);
		$num = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);
		
		$startDate = $date[1].'-'.$date[0].'-01';
		$endDate = $date[1].'-'.$date[0].'-'.$num;
		
		$query = $this->moonCalendar->select();
		$query->setIntegrityCheck(false);
		$adapter = $this->moonCalendar->getAdapter(); 
		$query->from(array('mdc'=>'moon_day_calendar'))
		->joinLeft(array('md' => 'moon_day'), 'md.id = mdc.moon_day_id',array('image'))
		->joinLeft(array('mc' => 'moon_calendar'), 'mc.id = mdc.moon_calendar_id',array('day_date'))
		->where($adapter->quoteInto('mc.day_date >= ?', $startDate))
		->where($adapter->quoteInto('mc.day_date <= ?', $endDate));
		//var_dump($query->assemble()); die;
		
		$stm = $query->query(Zend_Db::FETCH_ASSOC);
		$days = $stm->fetchAll();
		
		$monthes = array(
				'1' => 'января',
				'01' => 'января',
				'2' => 'февраля',
				'02' => 'февраля',
				'3' => 'марта',
				'03' => 'марта',
				'4' => 'апреля',
				'04' => 'апреля',
				'5' => 'мая',
				'05' => 'мая',
				'6' => 'июня',
				'06' => 'июня',
				'7' => 'июля',
				'07' => 'июля',
				'8' => 'августа',
				'08' => 'августа',
				'9' => 'сентября',
				'09' => 'сентября',
				'10' => 'октября',
				'11' => 'ноября',
				'12' => 'декабря',
		);
		$month = $monthes[$date[0]];
		//var_dump($month); die;
		
		$data = array();
		for($i = 1; $i < ($num +1); $i++){
			foreach($days as $day){
				$dayData = '';
				if($i >= 10){
					$dayData = $i;
				}else{
					$dayData = '0'.$i;
				}
				if($day['day_date'] == $date[1].'-'.$date[0].'-'.$dayData){
					$data[$i] = array(
						'image' 	=> $day['image'],
						'day_date' 	=> $day['day_date'],
						'day' => $i.' '.$month
					);
				}
			}
		}
		/*
		echo '<pre>';
		var_dump($data);
		*/ 
		$columns = array('first' => array(),'second' => array(),'third' => array());
		foreach($data as $index => $item){
			if($index <= 11){
				$columns['first'][] = $item;
			}
			if($index >= 12 && $index <= 22){
				$columns['second'][] = $item;
			}
			if($index >= 23){
				$columns['third'][] = $item;
			}
		}
		/*
		echo '<pre>';
		var_dump($columns); die;
		*/
		return $columns;
	}
}