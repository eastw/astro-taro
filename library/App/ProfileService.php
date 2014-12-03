<?php
class App_ProfileService{
	
	protected $userdata;
	protected $horoscopeService;
	protected $numerologyService;
	protected $users;
	protected $favorite;
	
	
	public function __construct($userdata){
		$this->userdata = $userdata;
		$this->horoscopeService = new App_HoroscopeService();
		$this->numerologyService = new App_NumerologyService();
		$this->users = new Application_Model_DbTable_Users();
		$this->favorite = new Application_Model_DbTable_FavoriteTable();
	}
	
	public function calcProfileParts(){
		$profile = new stdClass();
		$profile->parts = array();
		
		$profile->parts[] = $this->calcSunSign();
		$profile->parts[] = $this->calcLifeNumber();
		//var_dump($this->calcLifeNumber()); die;
		$profile->parts[] = $this->calcTaro();
		$profile->parts[] = $this->calcKarma();
		$profile->parts[] = $this->calcKelt();
		$profile->parts[] = $this->calcChina();
		
		return $profile;
	}
	
	protected function calcSunSign(){
		$data = array(); 
		$result = array();
		$result['name'] = 'Солнечный знак';
		if(null !== $this->userdata->birthday){ 
			
			if( $this->userdata->sun_sign_id == 0){
				$date = new Zend_Date($this->userdata->birthday);
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
				$data = $this->horoscopeService->getSignByAlias(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_SUN,$alias);
				$updateData = array(
					'sun_sign_id' => $data['id'],
					'sun_sign_alias' => $alias
				);
				$this->users->update($updateData,$this->users->getAdapter()->quoteInto('id=?',$this->userdata->id));
				$storage = Zend_Auth::getInstance()->getStorage()->read();
				$storage->sun_sign_id = $data['id'];
				$storage->sun_sign_alias = $alias;
				Zend_Auth::getInstance()->getStorage()->write($storage);
			}else{
				$data = $this->horoscopeService->getSign(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_SUN, $this->userdata->sun_sign_id);
			}
			$result['value'] = $data['sign_ru'];
			/*
			if(null === $data['image'] ){
				$result['image'] = 'astro-quest.png';
			}else{
				$result['image'] = $data['image'];
			}
			*/
			$result['image'] = '/files/images/profile/sun/'.$data['sign'].'.png';
			$result['desc'] = $data['text_date_range'];
			$result['type'] = 'sun';
		}else{
			$result['image'] = '/files/images/astro-quest.png';
			$result['empty'] = 'Необходимо заполнить дату рождения в профиле';
		}
		return $result;
	}
	
	protected function calcLifeNumber(){
		$result = array();
		$result['name'] = 'Число жизненного пути';
		$result['type'] = 'number';
		
		$data = array();
		if(!empty($this->userdata->birthday) ){
			if(empty($this->userdata->lifenumber)){
				$data = $this->numerologyService->getPersonalNumberByBirthdayAndName($this->userdata->birthday, $this->userdata->fullname, 'lifepath');
				$updateData = array(
						'lifenumber' => $data['number']
				);
				//var_dump($updateData); die;
				$this->users->update($updateData,$this->users->getAdapter()->quoteInto('id=?',$this->userdata->id));
				$storage = Zend_Auth::getInstance()->getStorage()->read();
				$storage->lifenumber = $data['number'];
				Zend_Auth::getInstance()->getStorage()->write($storage);
				//var_dump($this->userdata->lifenumber); die;
			}else{
				$data = $this->numerologyService->getPersonalNumber($this->userdata->lifenumber, App_NumerologyService::PERSONAL_LIFEPATH);
			}
			$result['value'] = App_UtilsService::numberToString($data['number']);
			$result['image'] = '/files/images/profile/number/'.$data['number'].'.png';
			$date = new Zend_Date($this->userdata->birthday);
			$result['desc'] = $date->toString(Zend_Date::DATE_LONG);
		}else{
			$result['image'] = '/files/images/astro-quest.png';
			$result['empty'] = 'Необходимо заполнить дату рождения в профиле';
		}
		
		/*
		if(!empty($this->userdata->birthday)){
			$numbers = str_replace('-','',$this->userdata->birthday);
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
					$result['image'] = 'astro-quest.png';
				}else{
					$result['image'] = 'astro-quest.png';
					break;
				}
			}
			$result['value'] = $number;
			$date = new Zend_Date($this->userdata->birthday);
			$result['desc'] = $date->toString(Zend_Date::DATE_LONG);
		}else{
			$result['empty'] = 'Необходимо заполнить дату рождения в профиле';
		}
		*/
		return $result;
	}
	
	protected function calcTaro(){
		$result = array();
		
		$result['name'] = 'Таро личности';
		$result['type'] = 'taro';
		if(!empty($this->userdata->birthday)){
			$numbers = str_replace('-','',$this->userdata->birthday);
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
				
				//$result['image'] = 'astro-quest.png';
				
			}
			$result['image'] = '/files/images/profile/taro/'.$number.'.png';
			$result['number'] = $number;
			
			switch($number){
				case 1: $result['value'] = 'Маг'; break;
				case 2: $result['value'] = 'Жрица'; break;
				case 3: $result['value'] = 'Императрица'; break;
				case 4: $result['value'] = 'Император'; break;
				case 5: $result['value'] = 'Иерофант'; break;
				case 6: $result['value'] = 'Влюбленные'; break;
				case 7: $result['value'] = 'Колесница'; break;
				case 8: $result['value'] = 'Сила'; break;
				case 9: $result['value'] = 'Отшельник'; break;
			}
			//var_dump($number); die;
			$result['desc'] = $number.'-й аркан';
		}else{
			$result['image'] = '/files/images/astro-quest.png';
			$result['empty'] = 'Небходимо заполнить дату рождения в профиле';
		}
		//var_dump($result); die;
		return $result;
	}
	
	protected function calcKarma(){
		$result = array();
		$result['image'] = '/files/images/astro-quest.png';
		$result['name'] = 'Кармический гороскоп';
		$result['type'] = 'karma';
		if(null !== $this->userdata->birthday){
			if( $this->userdata->horoscope_karma_id == 0){
				$data = $this->horoscopeService->getKarmaByBirthday($this->userdata->birthday);
				$updateData = array(
						'horoscope_karma_id' => $data['id']
				);
				$this->users->update($updateData,$this->users->getAdapter()->quoteInto('id=?',$this->userdata->id));
				$storage = Zend_Auth::getInstance()->getStorage()->read();
				$storage->horoscope_karma_id = $data['id'];
				Zend_Auth::getInstance()->getStorage()->write($storage);
			}else{
				$data = $this->horoscopeService->getKarmaPeriodById($this->userdata->horoscope_karma_id);
			}
			$result['desc'] = 'На момент вашего рождения Сатурн находился в знаке '.$data['sign_ru'];
			if($data['is_retrograd'] == 'y'){
				$result['desc'] .= ' (ретроградный период)';
			}
			$result['value'] = $data['sign_ru'];
			$result['image'] = '/files/images/profile/karma/'.$data['sign'].'.png';
		}else{
			$result['empty'] = 'Необходимо заполнить дату рождения в профиле';
		}
		//var_dump($result); die;
		/*	
		if(!empty($this->userdata->birthday)){
			$date = new Zend_Date($this->userdata->birthday);
			$year = (int)$date->get(Zend_Date::YEAR);
			$month = (int)$date->get(Zend_Date::MONTH);
			$day = (int)$date->get(Zend_Date::DAY);
			$result['desc'] = '';
			//var_dump($year); 
			if($this->isInRange('1940-01-01', '1940-03-20')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Овне';
				return $result;
			}
			if($this->isInRange('1940-03-21', '1942-05-08')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Тельце';
				return $result;
			}
			if($this->isInRange('1942-05-09', '1944-06-20')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Близнецах';
				return $result;
			}
			if($this->isInRange('1944-06-21', '1946-08-02')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Раке';
				return $result;
			}
			if($this->isInRange('1946-08-03', '1948-09-19')){
				$result['value'] = 'На момент вашего рождения Сатурн находился во Льве';
				return $result;
			}
			if($this->isInRange('1948-09-20', '1949-04-03')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Деве';
				return $result;
			}
			if($this->isInRange('1949-04-04', '1949-05-29')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Льве (ретроградный период)';
				return $result;
			}
			if($this->isInRange('1949-05-30', '1950-11-20')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Деве';
				return $result;
			}
			if($this->isInRange('1950-11-21', '1951-03-07')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Весах';
				return $result;
			}
			if($this->isInRange('1951-03-08', '1951-08-13')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Деве (ретроградный период)';
				return $result;
			}
			if($this->isInRange('1951-08-14', '1953-10-22')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Весах';
				return $result;
			}
			if($this->isInRange('1953-10-23', '1956-01-12')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Скорпионе';
				return $result;
			}
			if($this->isInRange('1956-01-13', '1956-05-14')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Стрельце';
				return $result;
			}
			if($this->isInRange('1956-05-15', '1956-10-10')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Скорпионе (ретроградный)';
				return $result;
			}
			if($this->isInRange('1956-10-11', '1959-01-05')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Стрельце';
				return $result;
			}
			if($this->isInRange('1959-01-06', '1962-01-03')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Козероге';
				return $result;
			}
			if($this->isInRange('1962-01-04', '1964-03-24')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Водолее';
				return $result;
			}
			if($this->isInRange('1964-03-25', '1964-09-17')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Рыбах';
				return $result;
			}
			if($this->isInRange('1964-09-18', '1964-12-16')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Водолее(ретроградный)';
				return $result;
			}
			//-----------------------------------------------------
			if($this->isInRange('1964-12-17', '1967-03-04')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Рыбах';
				return $result;
			}
			if($this->isInRange('1967-03-05', '1969-04-30')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Овне';
				return $result;
			}
			if($this->isInRange('1969-05-01', '1971-06-18')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Тельце';
				return $result;
			}
			if($this->isInRange('1971-06-19', '1972-01-10')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Близнецах';
				return $result;
			}
			if($this->isInRange('1972-01-11', '1972-02-21')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Тельце(ретроградный период)';
				return $result;
			}
			if($this->isInRange('1972-02-22', '1973-08-02')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Близнецах';
				return $result;
			}
			if($this->isInRange('1973-08-03', '1974-01-07')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Раке';
				return $result;
			}
			if($this->isInRange('1974-01-08', '1974-04-19')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Близнецах (ретроградный период)';
				return $result;
			}
			if($this->isInRange('1974-04-20', '1975-09-17')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Раке';
				return $result;
			}
			if($this->isInRange('1975-09-18', '1976-01-14')){
				$result['value'] = 'На момент вашего рождения Сатурн находился во Льве';
				return $result;
			}
			if($this->isInRange('1976-01-15', '1976-06-05')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Раке (ретроградный период)';
				return $result;
			}
			if($this->isInRange('1976-06-06', '1977-11-17')){
				$result['value'] = 'На момент вашего рождения Сатурн находился во Льве';
				return $result;
			}
			if($this->isInRange('1977-11-18', '1978-01-05')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Деве';
				return $result;
			}
			if($this->isInRange('1978-01-06', '1978-07-26')){
				$result['value'] = 'На момент вашего рождения Сатурн находился во Льве (ретроградный)';
				return $result;
			}
			if($this->isInRange('1978-07-27', '1980-09-21')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Деве';
				return $result;
			}
			if($this->isInRange('1980-09-22', '1982-11-29')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Весах';
				return $result;
			}
			if($this->isInRange('1982-11-30', '1983-05-06')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Скорпионе';
				return $result;
			}
			if($this->isInRange('1983-05-07', '1983-08-24')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Весах (ретроградный период)';
				return $result;
			}
			if($this->isInRange('1983-08-25', '1985-11-17')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Скорпионе';
				return $result;
			}
			if($this->isInRange('1985-11-18', '1988-02-14')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Стрельце';
				return $result;
			}
			if($this->isInRange('1988-02-15', '1988-06-10')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Козероге';
				return $result;
			}
			if($this->isInRange('1988-06-11', '1988-11-12')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Стрельце (ретроградный период)';
				return $result;
			}
			if($this->isInRange('1988-11-13', '1991-02-06')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Козероге';
				return $result;
			}
			if($this->isInRange('1991-02-07', '1993-05-21')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Водолее';
				return $result;
			}
			if($this->isInRange('1993-05-22', '1993-06-30')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Рыбах';
				return $result;
			}
			if($this->isInRange('1993-07-01', '1994-01-29')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Водолее (ретроградный период)';
				return $result;
			}
			if($this->isInRange('1994-01-30', '1996-04-07')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Рыбах';
				return $result;
			}
			if($this->isInRange('1996-04-08', '1998-06-09')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Овне';
				return $result;
			}
			if($this->isInRange('1998-06-10', '1998-10-25')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Тельце';
				return $result;
			}
			if($this->isInRange('1998-10-26', '1999-03-01')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Овне (ретроградный период)';
				return $result;
			}
			if($this->isInRange('1999-03-02', '2000-08-10')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Тельце';
				return $result;
			}
			if($this->isInRange('2000-08-11', '2000-10-16')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Близнецах';
				return $result;
			}
			if($this->isInRange('2000-10-17', '2001-04-21')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Тельце (ретроградный)';
				return $result;
			}
			if($this->isInRange('2001-04-22', '2003-06-04')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Близнецах';
				return $result;
			}
			if($this->isInRange('2003-06-05', '2005-07-16')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Раке';
				return $result;
			}
			if($this->isInRange('2005-07-17', '2007-09-02')){
				$result['value'] = 'На момент вашего рождения Сатурн находился во Льве';
				return $result;
			}
			if($this->isInRange('2007-09-03', '2009-10-29')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Деве';
				return $result;
			}
			if($this->isInRange('2009-10-30', '2010-04-07')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Весах';
				return $result;
			}
			if($this->isInRange('2010-04-08', '2010-07-21')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Деве (ретроградный период)';
				return $result;
			}
			if($this->isInRange('2010-07-22', '2012-10-05')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Весах';
				return $result;
			}
			if($this->isInRange('2012-10-06', '2014-12-23')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Скорпионе';
				return $result;
			}
			if($this->isInRange('2014-12-24', '2015-06-14')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Стрельце';
				return $result;
			}
			if($this->isInRange('2015-06-15', '2015-09-17')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Скорпионе (ретроградный период)';
				return $result;
			}
			if($this->isInRange('2015-09-18', '2017-12-20')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Стрельце';
				return $result;
			}
			if($this->isInRange('2017-12-21', '2020-03-22')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Козероге';
				return $result;
			}
			if($this->isInRange('2020-03-23', '2020-07-01')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Водолее';
				return $result;
			}
			if($this->isInRange('2020-07-02', '2020-12-18')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Козероге (ретроградный период)';
				return $result;
			}
			if($this->isInRange('2020-12-19', '2023-03-07')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Водолее';
				return $result;
			}
			if($this->isInRange('2023-03-08', '2025-05-27')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Рыбах';
				return $result;
			}
			if($this->isInRange('2025-05-28', '2025-09-21')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Овне';
				return $result;
			}
			if($this->isInRange('2025-09-22', '2026-02-14')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Рыбах (ретроградный период)';
				return $result;
			}
			if($this->isInRange('2026-02-15', '2028-04-14')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Овне';
				return $result;
			}
			if($this->isInRange('2028-04-15', '2030-06-02')){
				$result['value'] = 'На момент вашего рождения Сатурн находился в Тельце';
			}
			//echo '!!!!!';
		}else{
			$result['empty'] = 'Необходимо заполнить дату рождения в профиле';
		}
		*/
		return $result;
	}
	
	protected function isInRange($start,$end){
		$start = explode('-',$start);
		$start = mktime(0,0,0,$start[1],$start[2],$start[0]);
		
		$end = explode('-',$end);
		$end = mktime(0,0,0,$end[1],$end[2],$end[0]);
		
		$test = explode('-',$this->userdata->birthday);
		$test = mktime(0,0,0,$test[1],$test[2],$test[0]);
		
		if($start < $test && $test < $end ){
			return true;
		}
		return false;
	}
	
	protected function calcKelt(){
		$result = array();
		$result['name'] = 'Кельтский гороскоп';
		$result['type'] = 'kelt';
		$result['image'] = '/files/images/astro-quest.png';
		if(!empty($this->userdata->birthday)){
			$date = new Zend_Date($this->userdata->birthday);
			$month = $date->get(Zend_Date::MONTH);
			$day = $date->get(Zend_Date::DAY);
			$alias = '';	
			if(null !== $this->userdata->birthday){
				if( $this->userdata->kelt_sign_id == 0){
					if( ($month == 12  && $day >= 24) || ($month == 1  && $day <= 20 ) ){
						$alias = 'deer';
					}
					if( ($month == 1  && $day >= 21) || ($month == 2  && $day <= 17 ) ){
						$alias = 'crane';
					}
					if( ($month == 2  && $day >= 18) || ($month == 3  && $day <= 17 ) ){
						$alias = 'seal';
					}
					if( ($month == 3  && $day >= 18) || ($month == 4  && $day <= 14 ) ){
						$alias = 'bear';
					}
					if( ($month == 4  && $day >= 15) || ($month == 5  && $day <= 12 ) ){
						$alias = 'snake';
					}
					if( ($month == 5  && $day >= 13) || ($month == 6  && $day <= 9 ) ){
						$alias = 'bee';
					}
					if( ($month == 6  && $day >= 10) || ($month == 7  && $day <= 7 ) ){
						$alias = 'otter';
					}
					if( ($month == 7  && $day >= 8) || ($month == 8  && $day <= 4 ) ){
						$alias = 'cat';
					}
					if( ($month == 8  && $day >= 5) || ($month == 9  && $day <= 1 ) ){
						$alias = 'salmon';
					}
					if( ($month == 9  && $day >= 2) || ($month == 9  && $day <= 29 ) ){
						$alias = 'swan';
					}
					if( ($month == 9  && $day >= 30) || ($month == 10  && $day <= 27 ) ){
						$alias = 'goose';
					}
					if( ($month == 10  && $day >= 28) || ($month == 11  && $day <= 24 ) ){
						$alias = 'owl';
					}
					if( ($month == 11  && $day >= 25) || ($month == 12  && $day <= 23 ) ){
						$alias = 'raven';
					}
					//$data = $this->horoscopeService->getKarmaByBirthday($this->userdata->birthday);
					$data = $this->horoscopeService->getSignByAlias(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_KELT,$alias);
					$updateData = array(
							'kelt_sign_id' => $data['id']
					);
					$this->users->update($updateData,$this->users->getAdapter()->quoteInto('id=?',$this->userdata->id));
					$storage = Zend_Auth::getInstance()->getStorage()->read();
					$storage->kelt_sign_id = $data['id'];
					Zend_Auth::getInstance()->getStorage()->write($storage);
				}else{
					$data = $this->horoscopeService->getSign(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_SUN, $this->userdata->kelt_sign_id);
				}
				$result['value'] = $data['sign_ru'];
				//var_dump($data); die;
				/*
				if(null === $data['image'] ){
					$result['image'] = 'astro-quest.png';
				}else{
					$result['image'] = '/files/images/profile/kelt/'.$data['image'].'.png';
				}
				*/
				$result['image'] = '/files/images/profile/kelt/'.$data['sign'].'.png';
				$result['desc'] = $data['text_date_range'];
				$result['type'] = 'kelt';
			}else{
				$result['empty'] = 'Необходимо заполнить дату рождения в профиле';
			}
		}else{
			$result['empty'] = 'Необходимо заполнить дату рождения в профиле';
		}
		return $result;
	}
	
	protected function calcChina(){
		$result = array();
		$result['name'] = 'Китайский гороскоп';
		$result['type'] = 'china';
		$result['image'] = '/files/images/astro-quest.png';
		
		if(!empty($this->userdata->birthday)){
			$date = new Zend_Date($this->userdata->birthday);
			$year = $date->get(Zend_Date::YEAR);
			$data = array();
			if( $this->userdata->china_sign_id == 0){
				$tmp = $year - 1900;
				$number = $tmp % 12;
				$alias = '';
				$china_type_alias = '';
				switch($number){
					case 0: $alias='rat'; break;
					case 1: $alias = 'bull'; break;
					case 2: $alias = 'tiger'; break;
					case 3: $alias = 'rabbit'; break;
					case 4: $alias = 'dragon'; break;
					case 5: $alias = 'china_sneak'; break;
					case 6: $alias = 'horse'; break;
					case 7: $alias = 'sheep'; break;
					case 8: $alias = 'monkey'; break;
					case 9: $alias = 'chiken'; break;
					case 10: $alias = 'dog'; break;
					case 11: $alias = 'pig'; break;
				}
				
				$element = 0;
				if($tmp % 2 == 0){
					$element = $tmp % 10;
				}else{
					$tmp --;
					$element = $tmp % 10;
				}
				$china_type = $element;
				$data = $this->horoscopeService->getSignByAlias(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_CHINA, $alias,$china_type);
				//echo 'id = 0';
				//var_dump($data); die;
				$updateData = array(
						'china_sign_id' => $data['sign_id'],
						'china_sign_type_id' => $china_type
				);
				$this->users->update($updateData,$this->users->getAdapter()->quoteInto('id=?',$this->userdata->id));
				$storage = Zend_Auth::getInstance()->getStorage()->read();
				$storage->china_sign_id = $data['sign_id'];
				$storage->china_sign_type_id = $china_type;
				Zend_Auth::getInstance()->getStorage()->write($storage);
			}else{
				//echo 'id != 0';
				$data = $this->horoscopeService->getSign(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_CHINA, $this->userdata->china_sign_id,$this->userdata->china_sign_type_id);
				//var_dump($data); die;
			}
			//var_dump($data); die;
			$result['value'] = $data['sign_ru'].', стихия: '.$data['type_ru'];
			/*
			if(empty($data['image'] )){
				$result['image'] = 'astro-quest.png';
			}else{
				$result['image'] = $data['image'];
			}
			*/
			$result['image'] = '/files/images/profile/china/'.$data['sign'].'.png';
			$result['desc'] = $year;
			$result['type'] = 'china';
		}else{
			$result['empty'] = 'Необходимо заполнить дату рождения в профиле';
		}
		/*
		if(!empty($this->userdata->birthday)){
			$date = new Zend_Date($this->userdata->birthday);
			$year = $date->get(Zend_Date::YEAR);
			
			
			$tmp = $year - 1900;
			$number = $tmp % 12;
			
			switch($number){
				case 0: $result['value'] = 'Крыса'; break;
				case 1: $result['value'] = 'Бык'; break;
				case 2: $result['value'] = 'Тигр'; break;
				case 3: $result['value'] = 'Кролик'; break;
				case 4: $result['value'] = 'Дракон'; break;
				case 5: $result['value'] = 'Змея'; break;
				case 6: $result['value'] = 'Лошадь'; break;
				case 7: $result['value'] = 'Овца'; break;
				case 8: $result['value'] = 'Обезьяна'; break;
				case 9: $result['value'] = 'Петух'; break;
				case 10: $result['value'] = 'Собака'; break;
				case 11: $result['value'] = 'Свинья'; break;
			}
			
			$element = 0;
			if($tmp % 2 == 0){
				$element = $tmp % 10;
			}else{
				$tmp --;
				$element = $tmp % 10;
			}
			switch($element){
				case 0: $result['value'] .= ', cтихия: металл'; $result['image'] = 'astro-quest.png'; break;
				case 2: $result['value'] .= ', cтихия: вода'; $result['image'] = 'astro-quest.png'; break;
				case 4: $result['value'] .= ', cтихия: дерево'; $result['image'] = 'astro-quest.png'; break;
				case 6: $result['value'] .= ', cтихия: огонь'; $result['image'] = 'astro-quest.png'; break;
				case 8: $result['value'] .= ', cтихия: земля'; $result['image'] = 'astro-quest.png'; break;
			}
			$result['desc'] = $year;
			
		}else{
			$result['empty'] = 'Необходимо заполнить дату рождения в профиле';
		}
		*/
		return $result;
	}
	
	public function resizeAvatar(){
		
	}
	
	public function getSignDescription($type){
		switch($type){
			case 'sun'		: return $this->getSunDescription(); break;
			case 'number'	: return $this->getNumberDescription(); break;
			case 'taro'		: return $this->getTaroDescription(); break;
			case 'karma'	: return $this->getKarmaDescription(); break;
			case 'kelt'		: return $this->getKeltDescription(); break;
			case 'china'	: return $this->getChinaDescription(); break;
			default: throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404); 
		}
	}
	
	protected function getSunDescription(){
		$data = $this->horoscopeService->getSign(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_SUN, $this->userdata->sun_sign_id);
		if(null === $data['image']){
			$data['image'] = 'astro-quest.png';
		}else{
			$data['image'] = '/files/images/'.$data['image'];
		}
		$data['pageTitle'] = $data['sign_ru'];
		//var_dump($data); die;
		return $data;
		//return $data['description'];
	}
	protected function getNumberDescription(){
		$data = $this->numerologyService->getPersonalNumber($this->userdata->lifenumber, App_NumerologyService::PERSONAL_LIFEPATH);
		$data['image'] = '/files/images/profile/number/'.$data['number'].'.png';
		$data['pageTitle'] = App_UtilsService::numberToString($data['number']);
		$date = new Zend_Date($this->userdata->register_date);
		$data['value'] = $date->toString(Zend_Date::DATE_LONG);
		return $data;
	}
	protected function getTaroDescription(){
		$indexService = new App_IndexService();
		$tmpData = $this->calcTaro();
		//var_dump($tmpData); die;
		$taroData = $indexService->taroDay(($tmpData['number']+1), 1);
		
		$data = array();
		//$data['image'] = $taroData['taroDayImage'];
		$data['image'] = '/files/images/profile/taro/'.($tmpData['number']).'.png';
		$data['pageTitle'] = $tmpData['value'];
		$data['description'] = $taroData['description'];
		$data['value'] = $tmpData['desc'];
		//var_dump($data); die;
		return $data;
	}
	protected function getKarmaDescription(){
		$data = $this->horoscopeService->getKarmaPeriodById($this->userdata->horoscope_karma_id);
		//var_dump($data); die;
		$data['image'] = '/files/images/profile/karma/'.$data['sign'].'.png';
		$data['pageTitle'] = $data['sign_ru'];
		$data['value'] = 'На момент Вашего рождения Сатурн находился в знаке '.$data['sign_ru'];
		return $data;
	}
	protected function getKeltDescription(){
		$data = $this->horoscopeService->getSign(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_KELT, $this->userdata->kelt_sign_id);
		//var_dump($data); die;
		//if(null === $data['image']){
		$data['image'] = '/files/images/profile/kelt/'.$data['sign'].'.png';
		//}
		$data['pageTitle'] = $data['sign_ru'];
		$data['value'] = '';
		return $data;
	}
	protected function getChinaDescription(){
		$data = $this->horoscopeService->getSign(App_HoroscopeService::HOROSCOPE_SIGN_TYPE_CHINA, $this->userdata->china_sign_id,$this->userdata->china_sign_type_id);
		//var_dump($data); die;
		/*
		if(empty($data['image'])){
			$data['image'] = '/files/images/astro-quest.png';
		}
		*/
		$data['image'] = '/files/images/profile/china/'.$data['sign'].'.png';
		$data['pageTitle'] = $data['sign_ru'];
		$data['value'] = 'Стихия: '.$data['type_ru'];
		return $data;
	}
	
	public function getDayData($type,$dayData){
		$data = array();
		//var_dump($dayData);//die;
		//var_dump($type);
		if($type == 'taro'){
			$data['image'] = $dayData['taroDayImage'];
			
			if($dayData['state']){
				$data['title'] = $dayData['title'];
				$data['description'] = $dayData['description'];
			}else{
				$data['title'] = $dayData['title'].' (перевернутая)';
				$data['description'] = $dayData['description_reverse'];
			}
			//var_dump($data); die;
		}
		if($type == 'rune'){
			$data['image'] = $dayData['runeDayImage'];
			if($dayData['state']){
				$data['title'] = $dayData['title'];
				$data['description'] = $dayData['description'];
			}else{
				$data['title'] = $dayData['title'].' (перевернутая)';
				$data['description'] = $dayData['description_reverse'];
			}
		}
		if($type == 'hexagramm'){
			//var_dump($dayData); die;
			$data['image'] = $dayData['hexagrammDayImage'];
			$data['title'] = $dayData['title'];
			$data['order'] = $dayData['order'];
			$data['description'] = $dayData['description'];
			//var_dump($data); die;
		}
		if($type == 'number'){
			//var_dump($dayData); die;
			$data['image'] = '/files/images/profile/number/'.$dayData['number'].'.png';//$dayData['']
			$data['title'] = App_UtilsService::numberToString($dayData['number']);
			$data['description'] = $dayData['description'];
		}
		return $data;
	}
	
	public function addFavorite($id,$type,$user_id,$subtype){
		if(!$this->isFavorite($id, $type, $user_id)){
			$insertData = array(
				'user_id' => $user_id,
				'favorite_id' => $id,
				'favorite_subtype' => '',
				'date_added' => new Zend_Db_Expr('NOW()'),
			);
			if($type == 'divination' && is_numeric($id)){
				$divinationService = new App_DivinationService();
				$divination = $divinationService->getDivinationById($id);
				$categoryService = new App_CategoryService();
				$category = $categoryService->getCategory($divination['category_id'])->toArray();
				$insertData['favorite_type'] = 'divination';
				$insertData['favorite_name'] = $divination['name'];
				$insertData['favorite_link'] = '/gadaniya/'.$divination['type'].'/'.$category['alias'].'/'.$divination['alias'];
				$insertData['favorite_category_name'] = $category['name'];
			}
			if($type == 'article' && is_numeric($id)){
				$articleService = new App_ArticleService();
				$article = $articleService->getArticleById($id);
				$tagService = new App_TagService();
				$quicktags = explode(';',$article['quicktag']);
				$tag = $tagService->getTagById($quicktags[0]);
				$insertData['favorite_type'] = 'article';
				$insertData['favorite_name'] = $article['title'];
				$insertData['favorite_link'] = '/statyi/content/'.$tag['alias'].'/'.$article['alias'];//.'/'.$divination['alias'];
				$insertData['favorite_category_name'] = $tag['tagname'];
				//var_dump($insertData); die;
			}
			if($type == 'news'){
				$articleService = new App_ArticleService();
				$article = $articleService->getArticleById($id);
				$tagService = new App_TagService();
				$quicktags = explode(';',$article['quicktag']);
				$tag = $tagService->getTagById($quicktags[0]);
				$insertData['favorite_type'] = 'news';
				$insertData['favorite_name'] = $article['title'];
				$insertData['favorite_link'] = '/news/content/'.$tag['alias'].'/'.$article['alias'];//.'/'.$divination['alias'];
				$insertData['favorite_category_name'] = $tag['tagname'];
				//var_dump($insertData); die;
			}
			if($type == 'magic'){
				$articleService = new App_ArticleService();
				$article = $articleService->getArticleById($id);
				$tagService = new App_TagService();
				$quicktags = explode(';',$article['quicktag']);
				$tag = $tagService->getTagById($quicktags[0]);
				$insertData['favorite_type'] = 'magic';
				$insertData['favorite_name'] = $article['title'];
				$insertData['favorite_link'] = '/magic/content/'.$tag['alias'].'/'.$article['alias'];//.'/'.$divination['alias'];
				$insertData['favorite_category_name'] = $tag['tagname'];
				//var_dump($insertData); die;
			}
			$this->favorite->insert($insertData);
			return array('result'=>'added');
		}
		return array('result'=>'already added');
	}
	
	public function listFavorites($user_id){
		$query = $this->favorite->select();
		$query->from('user_favorite')
			->where($this->favorite->getAdapter()->quoteInto('user_id=?', $user_id))
			->order('date_added DESC');
		//var_dump($query->assemble()); die;	
		$stmt = $query->query(Zend_Db::FETCH_ASSOC);
		//return 
		$favorites = $stmt->fetchAll();
		foreach($favorites as $index => $favorite){
			if($favorite['favorite_type'] == 'divination'){
				$favorites[$index]['type_ru'] = 'Гадания';
				if($favorite['favorite_subtype'] == 'taro'){
					$favorites[$index]['type_ru'] .= ' \ Таро';
				}
				if($favorite['favorite_subtype'] == 'classic'){
					$favorites[$index]['type_ru'] .= ' \ Классические карты';
				}
				if($favorite['favorite_subtype'] == 'rune'){
					$favorites[$index]['type_ru'] .= ' \ Руны';
				}
				if($favorite['favorite_subtype'] == 'book'){
					$favorites[$index]['type_ru'] .= ' \ Книга перемен';
				}
				if($favorite['favorite_subtype'] == 'other'){
					$favorites[$index]['type_ru'] .= ' \ Другие гадания';
				}
				if(!empty($favorite['favorite_category_name'])){
					$favorites[$index]['type_ru'] .= ' \ '.$favorite['favorite_category_name'];
				}
			}
			if($favorite['favorite_type'] == 'article'){
				$favorites[$index]['type_ru'] = 'Статьи';
				$favorites[$index]['type_ru'] .= ' \ '.$favorite['favorite_category_name'];
			}
			if($favorite['favorite_type'] == 'news'){
				$favorites[$index]['type_ru'] = 'Новости';
				$favorites[$index]['type_ru'] .= ' \ '.$favorite['favorite_category_name'];
			}
			if($favorite['favorite_type'] == 'magic'){
				$favorites[$index]['type_ru'] = 'Магия';
				$favorites[$index]['type_ru'] .= ' \ '.$favorite['favorite_category_name'];
			}
		}
		return $favorites;
	}
	
	public function deleteFavorite($id){
		$this->favorite->delete($this->favorite->getAdapter()->quoteInto('id=?',$id));
	}
	
	public function isFavorite($id,$type,$user_id){
		$adapter = $this->favorite->getAdapter();
		$query = $this->favorite->select();
		$query->from('user_favorite')->where($adapter->quoteInto('favorite_id=?',$id))
			->where($adapter->quoteInto('favorite_type=?',$type))
			->where($adapter->quoteInto('user_id=?',$user_id));
		$stmt = $query->query(Zend_Db::FETCH_ASSOC);
		$favorite = $stmt->fetch();
		if($favorite){
			return true;
		}
		return false;
	}
	
	public function refreshFavoriteLink($id,$type){
		$updateData = array();
		if($type == 'divination'){
			$divinationService = new App_DivinationService();
			$divination = $divinationService->getDivinationById($id);
			$categoryService = new App_CategoryService();
			$category = $categoryService->getCategory($divination['category_id'])->toArray();
			$updateData['favorite_type'] = 'divination';
			$updateData['favorite_name'] = $divination['name'];
			$updateData['favorite_link'] = '/gadaniya/'.$divination['type'].'/'.$category['alias'].'/'.$divination['alias'];
			$updateData['favorite_category_name'] = $category['name'];
		}
		if($type == 'article'){
			$articleService = new App_ArticleService();
			$article = $articleService->getArticleById($id);
			$tagService = new App_TagService();
			$quicktags = explode(';',$article['quicktag']);
			$tag = $tagService->getTagById($quicktags[0]);
			$updateData['favorite_type'] = 'article';
			$updateData['favorite_name'] = $article['title'];
			$updateData['favorite_link'] = '/statyi/content/'.$tag['alias'].'/'.$article['alias'];
			$updateData['favorite_category_name'] = $tag['tagname'];
		}
		if($type == 'news'){
			$articleService = new App_ArticleService();
			$article = $articleService->getArticleById($id);
			$tagService = new App_TagService();
			$quicktags = explode(';',$article['quicktag']);
			$tag = $tagService->getTagById($quicktags[0]);
			$updateData['favorite_type'] = 'news';
			$updateData['favorite_name'] = $article['title'];
			$updateData['favorite_link'] = '/news/content/'.$tag['alias'].'/'.$article['alias'];
			$updateData['favorite_category_name'] = $tag['tagname'];
		}
		if($type == 'magic'){
			$articleService = new App_ArticleService();
			$article = $articleService->getArticleById($id);
			$tagService = new App_TagService();
			$quicktags = explode(';',$article['quicktag']);
			$tag = $tagService->getTagById($quicktags[0]);
			$updateData['favorite_type'] = 'magic';
			$updateData['favorite_name'] = $article['title'];
			$updateData['favorite_link'] = '/magic/content/'.$tag['alias'].'/'.$article['alias'];
			$updateData['favorite_category_name'] = $tag['tagname'];
		}
		$this->favorite->update($updateData, $this->favorite->getAdapter()->quoteInto('favorite_id=?',$id));
	}
}