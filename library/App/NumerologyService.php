<?php
class App_NumerologyService{
	const HUMEROLOGY_PERSONAL 		= 'personal';
	const HUMEROLOGY_TIME 			= 'time';
	const NUMEROLOGY_COMPABILITY 	= 'compability';
	
	const PERSONAL_LIFEPATH 		= 1;
	const PERSONAL_SELF_EXPRESSION 	= 2;
	const PERSONAL_IDENTITY 		= 3;
	const PERSONAL_SOUL 			= 4;
	const PERSONAL_ACHIEVEMENT 		= 5;
	const PERSONAL_KARMA			= 6;
	
	const TIME_DAY			= 7;
	const TIME_MONTH 		= 8;
	const TIME_YEAR			= 9;
	
	const COMPABILITY_LOVE		= 10;
	const COMPABILITY_PARTNER	= 11;
	
	const COMPABILITY_NUMBERTYPE_LIFEPATH = 1;
	const COMPABILITY_NUMBERTYPE_SELF_EXPRESSION = 2;
	const COMPABILITY_NUMBERTYPE_SOUL = 3;
	const COMPABILITY_NUMBERTYPE_IDENTITY = 4;
	
	protected $numerologyPersonal;
	protected $numerologyTime;
	protected $numerologyCompability;
	protected $numerologyTypes;
	protected $numerologyCompabilityTypes;
	protected $numerologyCompabilityPercent;
	
	protected $numerologySmallTypes;
	protected $numerologyBigTypes;
	protected $numerologySmallTypesAssociate;
	protected $numerologyFullTypes;
	
	protected $planetsAndWeekDays;
	
	public function __construct(){
		$this->numerologyPersonal = new Application_Model_DbTable_NumerologyPersonalTable();
		$this->numerologyTime = new Application_Model_DbTable_NumerologyTimeTable();
		$this->numerologyCompability = new Application_Model_DbTable_NumerologyCompabilityTable();
		$this->numerologyTypes = new Application_Model_DbTable_NumerologyTypeTable();
		$this->numerologyCompabilityTypes =new Application_Model_DbTable_NumerologyCompabilityTypeTable();
		$this->numerologyCompabilityPercent = new Application_Model_DbTable_NumerologyCompabilityPercentTable();

		$this->numerologyFullTypes = array(
				'personal' => array(
									'name' => 'Мои персональные числа',
									'small_desc' => 'Бесплатная нумерологическая карта личности',
									'image' => '/files/images/numerology_img1.jpg',
									'children' => array(
										'lifepath' => 'Число жизненного пути',
										'self-expression' => 'Число самовыражения',
										'identity' => 'Число личности',
										'soul' => 'Число души',
										'achievement' => 'Важные годы жизни',
										'karma' => 'Кармическая задача',
									)
							),
				'forecast' => array(
									'name' => 'Персональные прогнозы',
									'small_desc' => 'Бесплатные нумерологические прогнозы на период',
									'image' => '/files/images/numerology_img2.jpg',
									'children' => array(
										'day' => 'Персональный день',
										'month' => 'Персональный месяц',
										'year' => 'Персональный год'		
									),
								),
				'compability' => array(
									'name' => 'Нумерологическая совместимость',
									'small_desc' => 'Бесплатный нумерологический прогноз совместимости',
									'image' => '/files/images/numerology_img3.jpg',
									'children' => array(
											'love' => 'Любовная совместимость',
											'partner' => 'Партнерская совместимость',
									),
				),
		);
		$this->numerologySmallTypes = array(
				'lifepath','self-expression','identity','soul',
				'achievement','karma','day','month','year',
				'love','partner','no-smalltype');
		$this->numerologyBigTypes = array('personal','forecast','compability','list');
		$this->numerologySmallTypesAssociate = array(
				'lifepath' => 1,
				'self-expression' => 2,
				'identity' => 3,
				'soul' => 4,
				'achievement' => 5,
				'karma' => 6,
				
				'day' => 7,
				'month' => 8,
				'year' => 9,
				
				'love' => 10,
				'partner' => 11,
		);
		
		$this->planetsAndWeekDays = array(
				1 => array('Солнце','Воскресенье'),
				2 => array('Луна','Понедельник'),
				3 => array('Юпитер','Вторник'),
				4 => array('Раху','Среда'),
				5 => array('Меркурий','Четверг'),
				6 => array('Венера','Пятница'),
				7 => array('Кету','Суббота'),
				8 => array('Сатурн','Суббота'),
				9 => array('Марс','Вторник'),
				11 => array('Прозерипина','Суббота'),
				22 => array('Вулкан','Среда'),
		);
	}
	
	public function getTypes(){
		return $this->numerologyTypes->fetchAll()->toArray();
	}
	public function getCompabilityTypes(){
		return $this->numerologyCompabilityTypes->fetchAll()->toArray();
	}
	public function getFullNumerologyTypes(){
		return $this->numerologyFullTypes;
	}
	public function getSmallNumerologyTypes(){
		return $this->numerologySmallTypes;
	}
	public function getBigNumerologyTypes(){
		return $this->numerologyBigTypes;
	}
	public function getPersonalNumber($number,$type,$aloop=null){
		$adapter = $this->numerologyPersonal->getAdapter();
		$query = $adapter->select();
		$query->from('numerology_personal')
			->where($adapter->quoteInto('number=?', $number))
			->where($adapter->quoteInto('type_id=?',$type));
		/*
		if(!empty($aloop)){
			$query->where($adapter->quoteInto('aloop=?',$aloop)); 
		}
		*/
		$stm = $query->query();
		$line = $stm->fetch();
		if(!$line){
			$insertData = array(
				'number' 		=> $number,
				'type_id' 		=> $type,
				'description' 	=> '',
			);
			if(!empty($aloop)){
				$insertData['aloop'] = $aloop;
			}
			$insertData['id'] = $this->numerologyPersonal->insert($insertData);
			return $insertData;
		}
		return $line;
	}
	
	public function savePersonalNumber($number,$type,$aloop,$description){
		$adapter = $this->numerologyPersonal->getAdapter();
		$query = $adapter->select();
		$query->from('numerology_personal')
		->where($adapter->quoteInto('number=?', $number))
		->where($adapter->quoteInto('type_id=?',$type));
		if(!empty($aloop)){
			$query->where($adapter->quoteInto('aloop=?',$aloop));
		}
		$stm = $query->query();
		$line = $stm->fetch();
		if(!$line){
			$insertData = array(
					'number' 		=> $number,
					'type_id' 		=> $type,
					'description' 	=> $description,
			);
			if(!empty($aloop)){
				$insertData['aloop'] = $aloop;
			}
			$insertData['id'] = $this->numerologyPersonal->insert($insertData);
			return $insertData;
		}else{
			$line['description'] = $description;
			$this->numerologyPersonal->update($line, $adapter->quoteInto('id=?',$line['id'])); 
		}
	}
	
	protected function getUserPercentDescription($percent,$type){
		$adapter = $this->numerologyCompability->getAdapter();
		$query = $adapter->select();
		$query->from('numerology_compability_percent')
		->where($adapter->quoteInto('percent >= ?',$percent))
		->where($adapter->quoteInto('compability_type_id=?',$type))
		->order('percent ASC')->limit('1');
		//var_dump($query->assemble()); die;
		$stm = $query->query();
		$line = $stm->fetch();
		
		return $line['description'];
	}
	
	public function getCompability($numberType, $type,$number1,$number2){
		$adapter = $this->numerologyCompability->getAdapter();
		$query = $adapter->select();
		$query->from('numerology_compability')
		->where(' (number1 = '.$number1.' AND number2 = '.$number2.') OR (number1 = '.$number2.' AND number2 = '.$number1.')')
		->where($adapter->quoteInto('number_type_id=?',$numberType))
		->where($adapter->quoteInto('type_id=?',$type));
		//var_dump($query->assemble()); die;
		$stm = $query->query();
		$line = $stm->fetch();
		
		if(!$line){
			$insertData = array(
					'number1' 			=> $number2,
					'number2' 			=> $number1,
					'type_id' 			=> $type,
					'number_type_id' 	=> $numberType,
					'description' 		=> '',
			);
			$this->numerologyCompability->insert($insertData);
			$insertData = array(
					'number1' 			=> $number1,
					'number2' 			=> $number2,
					'type_id' 			=> $type,
					'number_type_id' 	=> $numberType,
					'description' 		=> '',
			);
			$insertData['id'] = $this->numerologyCompability->insert($insertData);
			return $insertData;
		}else{
			
			if($number1 != $line['number1']){
				$tmp = $line['number1'];
				$line['number1'] = $line['number2'];
				$line['number2'] = $tmp;
			}
		}
		//var_dump($line); die;
		return $line;
	}
	
	public function saveCompability($numberType, $type,$number1,$number2,$description,$comPercent){
		$adapter = $this->numerologyCompability->getAdapter();
		$query = $adapter->select();
		/*
		$query->from('numerology_compability')
			->where($adapter->quoteInto('number1=?', $number1))
			->where($adapter->quoteInto('number2=?', $number2))
			->where($adapter->quoteInto('number_type_id=?',$numberType))
			->where($adapter->quoteInto('type_id=?',$type));
		*/
		$query->from('numerology_compability')
		->where(' (number1 = '.$number1.' AND number2 = '.$number2.') OR (number1 = '.$number2.' AND number2 = '.$number1.')')
		->where($adapter->quoteInto('number_type_id=?',$numberType))
		->where($adapter->quoteInto('type_id=?',$type));
		//var_dump($query->assemble()); die;
		$stm = $query->query();
		$line = $stm->fetch();
		
		if(!$line){
			$insertData = array(
					'number1' 				=> $number2,
					'number2' 				=> $number1,
					'type_id' 				=> $type,
					'number_type_id' 		=> $numberType,
					'description' 			=> $description,
					'compability_percent' 	=> $compPercent
			);
			$this->numerologyCompability->insert($insertData);
			$insertData = array(
					'number1' 				=> $number1,
					'number2' 				=> $number2,
					'type_id' 				=> $type,
					'number_type_id' 		=> $numberType,
					'description' 			=> $description,
					'compability_percent' 	=> $compPercent
			);
			$insertData['id'] = $this->numerologyCompability->insert($insertData);
		}else{
			$stm = $query->query();
			$lines = $stm->fetchAll();
			//var_dump($lines); die;
			foreach($lines as $item){
				$item['description'] = $description;
				$item['compability_percent'] = $comPercent;
				$this->numerologyCompability->update($item, $adapter->quoteInto('id=?',$item['id']));
			}
		}
	}
	
	public function getPersonalNumberByBirthdayAndName($birthday,$name,$type){
		$type_id = 0;
		foreach($this->numerologySmallTypesAssociate as $key => $value){
			if($key == $type){
				$type_id = $value;
			}
		}
		$number = -1;
		switch ($type_id){
			case self::PERSONAL_LIFEPATH: $number = $this->calcLifepathNumber($birthday); break;  
			case self::PERSONAL_SELF_EXPRESSION: $number = $this->calcSelfExpressionNumber($name); break;
			case self::PERSONAL_IDENTITY: $number = $this->calcIdentityNumber($birthday,$name); break;
			case self::PERSONAL_SOUL: $number = $this->calcSoulNumber($birthday); break;
			case self::PERSONAL_ACHIEVEMENT: $number = $this->calcAchievementNumbers($birthday); break;
			case self::PERSONAL_KARMA: $number = $this->calcKarmaNumber($birthday); break;
		}
		if($type_id == self::PERSONAL_ACHIEVEMENT){
			$data = array();
			$count = 1;
			$data = $this->getPersonalNumber($number['desc_number'], $type_id,$count);
			$data['anumbers'] = $number['anumbers'];
			//var_dump($data); die;
			/*
			
			foreach($number as $num){
				$tmp = $this->getPersonalNumber($num['desc_number'], $type_id,$count);
				$tmp['number'] = $num['anumber'];
				$tmp['number_text'] = App_UtilsService::numberToString($tmp['number']);
				$tmp['planet'] = (isset($this->planetsAndWeekDays[$tmp['number']][0]))?$this->planetsAndWeekDays[$tmp['number']][0]:'';
				$tmp['dayWeek'] = (isset($this->planetsAndWeekDays[$tmp['number']][1]))?$this->planetsAndWeekDays[$tmp['number']][1]:'';
				
				$data[] = $tmp;
				$count++;
			}
			*/
			/*
			echo '<pre>';
			var_dump($data); 
			die;
			*/
			return $data;
		}
		$data = $this->getPersonalNumber($number, $type_id);
		$data['number_text'] = App_UtilsService::numberToString($data['number']);
		$data['planet'] = (isset($this->planetsAndWeekDays[$data['number']][0]))?$this->planetsAndWeekDays[$data['number']][0]:'';
		$data['dayWeek'] = (isset($this->planetsAndWeekDays[$data['number']][1]))?$this->planetsAndWeekDays[$data['number']][1]:'';
		//var_dump($data); die;
		return $data;
	}
	
	protected function calcLifepathNumber($birthday){
		$numbers = str_replace('-','',$birthday);
		$number = 0;
		for($i = 0,$n = strlen($numbers); $i < $n;$i++){
			$number += (int)$numbers[$i];
		}
		while($number > 9){
			if($number != 11 && $number != 22){
				$numbers = ''.$number;
				$number = 0;
				for($i = 0,$n = strlen($numbers); $i < $n;$i++){
					$number += (int)$numbers[$i];
				}
			}else{
				break;
			}
		}
		return $number;
	}
	
	protected function calcSelfExpressionNumber($fullname){
		$table = array(
				1 => array('а','и','с','ъ','А','И','С',''),
				2 => array('б','й','т','ы','Б','','Т',''),
				3 => array('в','к','у','ь','В','К','У',''),
				4 => array('г','л','ф','э','Г','Л','Ф','Э'),
				5 => array('д','м','х','ю','Д','М','Х','Ю'),
				6 => array('е','н','ц','я','Е','Н','Ц','Я'),
				7 => array('ё','о','ч','','Ё','О','Ч',''),
				8 => array('ж','п','ш','','Ж','П','Ш',''),
				9 => array('з','р','щ','','З','Р','Щ',''),
		);
		$names = explode(':',$fullname);
		$numbers = array();
		
		
		foreach($names as $name){
			$numbers[] = array();
			$index = count($numbers)-1;
			$name = iconv('utf-8','windows-1251',$name);
			for($i = 0,$n = strlen($name); $i < $n;$i++){
				foreach($table as $key => $value){
					foreach($value as $item){
						if(iconv('utf-8','windows-1251',$item) == $name[$i] ){
							$numbers[$index][] = $key;
						}
					}
				}
			}
		}
		//var_dump($numbers); die;
		$numbers_total = array();
		foreach($numbers as $item_array){
			$number = 0;
			for($i = 0,$n = count($item_array); $i < $n;$i++){
				$number += (int)$item_array[$i];
			}
			while($number > 9){
				$numbers = ''.$number;
				$number = 0;
				for($i = 0,$n = strlen($numbers); $i < $n;$i++){
					$number += (int)$numbers[$i];
				}
			}
			$numbers_total[] = $number;
		}
		$number = 0;
		foreach($numbers_total as $item){
			$number += $item;
		}
		while($number > 9){
			if($number != 11 && $number != 22){
				$numbers = ''.$number;
				$number = 0;
				for($i = 0,$n = strlen($numbers); $i < $n;$i++){
					$number += (int)$numbers[$i];
				}
			}else{
				break;
			}
		}
		return $number;
	}
	protected function calcIdentityNumber($birthday,$fullname){
		$lifenumber = $this->calcLifepathNumber($birthday);
		$selfExpressionNumber = $this->calcSelfExpressionNumber($fullname);
		$number = $lifenumber + $selfExpressionNumber;
		while($number > 9){
			if($number != 11 && $number != 22){
				$numbers = ''.$number;
				$number = 0;
				for($i = 0,$n = strlen($numbers); $i < $n;$i++){
					$number += (int)$numbers[$i];
				}
			}else{
				break;
			}
		}
		return $number;
	}
	protected function calcSoulNumber($birthday){
		$date = new Zend_Date($birthday);
		$day = $date->get(Zend_Date::DAY);
		if($day == '11' || $day == '22'){
			return $day;
		}
		$number = 0;
		for($i = 0,$n = strlen($day); $i < $n;$i++){
			$number += (int)$day[$i];
		}
		while($number > 9){
			if($number != 11 && $number != 22){
				$numbers = ''.$number;
				$number = 0;
				for($i = 0,$n = strlen($numbers); $i < $n;$i++){
					$number += (int)$numbers[$i];
				}
			}else{
				break;
			}
		}
		return $number;
	}
	
	protected function calcAchievementNumbers($birthday){
		$lifepath = $this->calcLifepathNumber($birthday);
		$anumber1 = 36 - $lifepath;
		$anumber2 = $anumber1 + 9;
		$anumber3 = $anumber2 + 9;
		$anumber4 = $anumber3 + 9;
		
		/*var_dump($anumber1);
		var_dump($anumber2);
		var_dump($anumber3);
		var_dump($anumber4);
		*/
		
		$year = date('Y',strtotime($birthday));
		$month = date('m',strtotime($birthday));
		$day = date('d',strtotime($birthday));
		
		$numbers1 = ($year + $anumber1).'-'.$month.'-'.$day;
		$desc_number = $this->calcAchievementNumber($numbers1);
		
		/*
		$numbers2 = ($year + $anumber2).'-'.$month.'-'.$day;
		$desc_number2 = $this->calcAchievementNumber($numbers2);
		
		$numbers3 = ($year + $anumber3).'-'.$month.'-'.$day;
		$desc_number3 = $this->calcAchievementNumber($numbers3);
		
		$numbers4 = ($year + $anumber4).'-'.$month.'-'.$day;
		$desc_number4 = $this->calcAchievementNumber($numbers4);
		*/
		//var_dump($numbers1);
		//var_dump($numbers2);
		//var_dump($numbers3);
		//var_dump($numbers4);
		
		return array('anumbers' => array($anumber1,$anumber2,$anumber3,$anumber4),'desc_number' => $desc_number);
					/*
					array($anumber1,'desc_number' => $desc_number1),
					array('anumber' => $anumber2,'desc_number' => $desc_number2),
					array('anumber' => $anumber3,'desc_number' => $desc_number3),
					array('anumber' => $anumber4,'desc_number' => $desc_number4));
					*/  
	}
	
	protected function calcAchievementNumber($birthday){
		//return $this->calcLifepathNumber($anumbers);
		$numbers = str_replace('-','',$birthday);
		$number = 0;
		for($i = 0,$n = strlen($numbers); $i < $n;$i++){
			$number += (int)$numbers[$i];
		}
		while($number > 9){
			$numbers = ''.$number;
			$number = 0;
			for($i = 0,$n = strlen($numbers); $i < $n;$i++){
				$number += (int)$numbers[$i];
			}
		}
		return $number;
	}
	
	protected function calcKarmaNumber($birthday){
		return $this->calcLifepathNumber($birthday);
	}
	
	public function getForecastByBirthdayAndDate($birthday,$pdate,$type){
		$type_id = 0;
		foreach($this->numerologySmallTypesAssociate as $key => $value){
			if($key == $type){
				$type_id = $value;
			}
		}
		$number = -1;
		switch ($type_id){
			case self::TIME_DAY: $number = $this->calcDayNumber($birthday,$pdate); break;
			case self::TIME_MONTH: $number = $this->calcMonthNumber($birthday,$pdate); break;
			case self::TIME_YEAR: $number = $this->calcYearNumber($birthday,$pdate); break;
		}
		//$tmp['number_text'] = App_UtilsService::numberToString($tmp['number']);
		$data = $this->getPersonalNumber($number,$type_id);
		$data['number_text'] = App_UtilsService::numberToString($data['number']);
		return $data;
	}
	
	protected function calcDayNumber($birthday,$pdate){
		$birthday_array = explode('-',$birthday);
		$numbers = $birthday_array[1].$birthday_array[2].str_replace('-','',$pdate);
		
		//$numbers = str_replace('-','',$birthday);
		$number = 0;
		for($i = 0,$n = strlen($numbers); $i < $n;$i++){
			$number += (int)$numbers[$i];
		}
		while($number > 9){
			$numbers = ''.$number;
			$number = 0;
			for($i = 0,$n = strlen($numbers); $i < $n;$i++){
				$number += (int)$numbers[$i];
			}
		}
		return $number;
	}
	
	protected function calcMonthNumber($birthday,$pdate){
		$birthday_array = explode('-',$birthday);
		$pdate_array = explode('-',$pdate);
		
		$numbers = $birthday_array[1].$birthday_array[2].$pdate_array[1].$pdate_array[0];
		$number = 0;
		for($i = 0,$n = strlen($numbers); $i < $n;$i++){
			$number += (int)$numbers[$i];
		}
		while($number > 9){
			$numbers = ''.$number;
			$number = 0;
			for($i = 0,$n = strlen($numbers); $i < $n;$i++){
				$number += (int)$numbers[$i];
			}
		}
		return $number;
	}
	
	protected function calcYearNumber($birthday,$pdate){
		$birthday_array = explode('-',$birthday);
		$pdate_array = explode('-',$pdate);
		
		$numbers = $birthday_array[1].$birthday_array[2].$pdate_array[0];
		$number = 0;
		for($i = 0,$n = strlen($numbers); $i < $n;$i++){
			$number += (int)$numbers[$i];
		}
		while($number > 9){
			$numbers = ''.$number;
			$number = 0;
			for($i = 0,$n = strlen($numbers); $i < $n;$i++){
				$number += (int)$numbers[$i];
			}
		}
		return $number;
	}
	
	public function getCompabilityData($birthday1,$birthday2,$fullname1,$fullname2,$type){
		$type_id = 0;
		foreach($this->numerologySmallTypesAssociate as $key => $value){
			if($key == $type){
				$type_id = $value;
			}
		}
		$number = -1;
		switch ($type_id){
			case self::COMPABILITY_LOVE: $data = $this->calcLoveCompabilityNumber($birthday1,$birthday2,$fullname1,$fullname2); break;
			case self::COMPABILITY_PARTNER: $data = $this->calcPartherCompabilityNumber($birthday1,$birthday2,$fullname1,$fullname2); break;
		}
		return $data;
	}
	
	protected function calcLoveCompabilityNumber($birthday1,$birthday2,$fullname1,$fullname2){
		
		$data = array();
		
		$lifeNumber1 = $this->calcLifepathNumber($birthday1);
		$lifeNumber2 = $this->calcLifepathNumber($birthday2);
		
		$data['lifepath'] = $this->getCompability(
					self::COMPABILITY_NUMBERTYPE_LIFEPATH, 
					self::COMPABILITY_LOVE, 
					$lifeNumber1, 
					$lifeNumber2);
		
		$selfExpressionNumber1 = $this->calcSelfExpressionNumber($fullname1);
		$selfExpressionNumber2 = $this->calcSelfExpressionNumber($fullname2);
		
		$data['selfexpression'] = $this->getCompability(
					self::COMPABILITY_NUMBERTYPE_SELF_EXPRESSION, 
					self::COMPABILITY_LOVE, 
					$selfExpressionNumber1, 
					$selfExpressionNumber2); 
		
		$identityNumber1 = $this->calcIdentityNumber($birthday1, $fullname1);
		$identityNumber2 = $this->calcIdentityNumber($birthday2, $fullname2);
		
		$data['identity'] = $this->getCompability(
				self::COMPABILITY_NUMBERTYPE_IDENTITY,
				self::COMPABILITY_LOVE,
				$identityNumber1,
				$identityNumber2);
		
		$soulNumber1 = $this->calcSoulNumber($birthday1);
		$soulNumber2 = $this->calcSoulNumber($birthday2);
		
		$data['soul'] = $this->getCompability(
				self::COMPABILITY_NUMBERTYPE_SOUL,
				self::COMPABILITY_LOVE,
				$soulNumber1,
				$soulNumber2);
		//echo '<pre>';
		//var_dump($data); die;
		
		$data['lifepath']['number_text1'] = App_UtilsService::numberToString($data['lifepath']['number1']);
		$data['lifepath']['number_text2'] = App_UtilsService::numberToString($data['lifepath']['number2']);
		$data['selfexpression']['number_text1'] = App_UtilsService::numberToString($data['selfexpression']['number1']);
		$data['selfexpression']['number_text2'] = App_UtilsService::numberToString($data['selfexpression']['number2']);
		$data['identity']['number_text1'] = App_UtilsService::numberToString($data['identity']['number2']);
		$data['identity']['number_text2'] = App_UtilsService::numberToString($data['identity']['number2']);
		$data['soul']['number_text1'] = App_UtilsService::numberToString($data['soul']['number1']);
		$data['soul']['number_text2'] = App_UtilsService::numberToString($data['soul']['number2']);
		
		if(
				(isset($data['lifepath']['compability_percent']) && null !== $data['lifepath']['compability_percent'])
			&& 	(isset($data['selfexpression']['compability_percent']) && null !== $data['selfexpression']['compability_percent'])
			&& 	(isset($data['identity']['compability_percent']) && null !== $data['identity']['compability_percent'])
			&& 	(isset($data['soul']['compability_percent']) && null !== $data['soul']['compability_percent'])
		){
			$data['percent'] = ($data['lifepath']['compability_percent']
					+$data['selfexpression']['compability_percent']
					+$data['identity']['compability_percent']
					+$data['soul']['compability_percent']) / 4;
		}else{
			$data['percent'] = 0;
		}
		$data['percent_description'] = $this->getUserPercentDescription($data['percent'],self::COMPABILITY_LOVE);
		//var_dump($data['percent']); die;
				
		
		return $data;
	}
	
	protected function calcPartherCompabilityNumber($birthday1,$birthday2,$fullname1,$fullname2){
		$data = array();
		
		$lifeNumber1 = $this->calcLifepathNumber($birthday1);
		$lifeNumber2 = $this->calcLifepathNumber($birthday2);
		
		$data['lifepath'] = $this->getCompability(
					self::COMPABILITY_NUMBERTYPE_LIFEPATH, 
					self::COMPABILITY_PARTNER, 
					$lifeNumber1, 
					$lifeNumber2);
		
		$selfExpressionNumber1 = $this->calcSelfExpressionNumber($fullname1);
		$selfExpressionNumber2 = $this->calcSelfExpressionNumber($fullname2);
		
		$data['selfexpression'] = $this->getCompability(
					self::COMPABILITY_NUMBERTYPE_SELF_EXPRESSION, 
					self::COMPABILITY_PARTNER, 
					$selfExpressionNumber1, 
					$selfExpressionNumber2); 
		
		$identityNumber1 = $this->calcIdentityNumber($birthday1, $fullname1);
		$identityNumber2 = $this->calcIdentityNumber($birthday2, $fullname2);
		
		$data['identity'] = $this->getCompability(
				self::COMPABILITY_NUMBERTYPE_IDENTITY,
				self::COMPABILITY_PARTNER,
				$identityNumber1,
				$identityNumber2);
		
		$soulNumber1 = $this->calcSoulNumber($birthday1);
		$soulNumber2 = $this->calcSoulNumber($birthday2);
		
		$data['soul'] = $this->getCompability(
				self::COMPABILITY_NUMBERTYPE_SOUL,
				self::COMPABILITY_PARTNER,
				$soulNumber1,
				$soulNumber2);
		
		$data['lifepath']['number_text1'] = App_UtilsService::numberToString($data['lifepath']['number1']);
		$data['lifepath']['number_text2'] = App_UtilsService::numberToString($data['lifepath']['number2']);
		$data['selfexpression']['number_text1'] = App_UtilsService::numberToString($data['selfexpression']['number1']);
		$data['selfexpression']['number_text2'] = App_UtilsService::numberToString($data['selfexpression']['number2']);
		$data['identity']['number_text1'] = App_UtilsService::numberToString($data['identity']['number2']);
		$data['identity']['number_text2'] = App_UtilsService::numberToString($data['identity']['number2']);
		$data['soul']['number_text1'] = App_UtilsService::numberToString($data['soul']['number1']);
		$data['soul']['number_text2'] = App_UtilsService::numberToString($data['soul']['number2']);
		
		if(
			(isset($data['lifepath']['compability_percent']) && null !== $data['lifepath']['compability_percent'])
			&& 	(isset($data['selfexpression']['compability_percent']) && null !== $data['selfexpression']['compability_percent'])
			&& 	(isset($data['identity']['compability_percent']) && null !== $data['identity']['compability_percent'])
			&& 	(isset($data['soul']['compability_percent']) && null !== $data['soul']['compability_percent'])
		){
			$data['percent'] = ($data['lifepath']['compability_percent']
					+$data['selfexpression']['compability_percent']
					+$data['identity']['compability_percent']
					+$data['soul']['compability_percent']) / 4;
		}else{
			$data['percent'] = 0;
		}
		$data['percent_description'] = $this->getUserPercentDescription($data['percent'],self::COMPABILITY_PARTNER);
		
		return $data;
	}

	public function getPercentDescription($type,$percent){
		$adapter = $this->numerologyCompability->getAdapter();
		$query = $adapter->select();
		$query->from('numerology_compability_percent')
		->where($adapter->quoteInto('percent=?',$percent))
		->where($adapter->quoteInto('compability_type_id=?',$type));
		//var_dump($query->assemble()); die;
		$stm = $query->query();
		$line = $stm->fetch();
		
		if(!$line){
			$insertData = array(
				'percent' => $percent,
				'compability_type_id' => $type,
				'description' => ''		
			);
			$insertData['id'] = $this->numerologyCompabilityPercent->insert($insertData);
			return $insertData;
		}
		return $line;
	}
	
	public function savePercentDescription($type,$percent,$description){
		$adapter = $this->numerologyCompability->getAdapter();
		$query = $adapter->select();
		$query->from('numerology_compability_percent')
		->where($adapter->quoteInto('percent=?',$percent))
		->where($adapter->quoteInto('compability_type_id=?',$type));
		//var_dump($query->assemble()); die;
		$stm = $query->query();
		$line = $stm->fetch();
		if(!$line){
			
			$insertData = array(
					'percent' => $percent,
					'compability_type_id' => $type,
					'description' => $description
			);
			var_dump($insertData); die;
			$insertData['id'] = $this->numerologyCompabilityPercent->insert($insertData);
		}
		$line['description'] = $description;
		$this->numerologyCompabilityPercent->update($line, $adapter->quoteInto('id=?', $line['id'])); 
	}
	
	public function calcTodayNumber($birthday){
		$number = $this->calcDayNumber($birthday, date('Y-m-d'));
		$data = $this->getPersonalNumber($number, self::TIME_DAY);
		
		$planetsAndWeekDays = array(
				1 => array('Солнце','Воскресенье'),
				2 => array('Луна','Понедельник'),
				3 => array('Марс','Вторник'),
				4 => array('Меркурий','Среда'),
				5 => array('Юпитер','Четверг'),
				6 => array('Венера','Пятница'),
				7 => array('Сатурн','Суббота'),
				8 => array('Уран','Среда'),
				9 => array('Нептун','Пятница'),
		);
		return $data;
	}
}