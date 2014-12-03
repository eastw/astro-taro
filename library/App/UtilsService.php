<?php
class App_UtilsService {
	
	public static $monthes = array(
			'01' => 'Январь',
			'02' => 'Февраль',
			'03' => 'Март',
			'04' => 'Апрель',
			'05' => 'Май',
			'06' => 'Июнь',
			'07' => 'Июль',
			'08' => 'Август',
			'09' => 'Сентябрь',
			'10' => 'Октябрь',
			'11' => 'Ноябрь',
			'12' => 'Декабрь',
	);
	
	public static function generateTranslit($phrase){
		//$phrase = preg_replace('/[^a-zA-ZА-Яа-я0-9\s]/ums', '', $phrase);
		$phrase = preg_replace('/[^a-zA-ZА-Яа-я0-9\s]/ums', '', $phrase);
		$tr = array(
                "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
                "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
                "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
                "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
                "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
                "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
                "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
                "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
                "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
                "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
                "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
                "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
                "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",' '=>'-',
				","=>'','.'=>'','"'=>'',"'"=>'',"”" =>'',"&gt;" => '',"“" =>'',
				"?"=>''
            );
        $phrase = strtolower(strtr($phrase,$tr));
        $phrase = preg_replace('/[^a-zA-Z0-9\-]/ums', '', $phrase);
        //var_dump($phrase); die;
        //var_dump($phrase); die;
		return  $phrase;
	}

	public static function getMonthByNumber($number){
		$monthes = array(
				'1' => 'январь',
				'01' => 'январь',
				'2' => 'февраль',
				'02' => 'февраль',
				'3' => 'март',
				'03' => 'март',
				'4' => 'апрель',
				'04' => 'апрель',
				'5' => 'май',
				'05' => 'май',
				'6' => 'июнь',
				'06' => 'июнь',
				'7' => 'июль',
				'07' => 'июль',
				'8' => 'август',
				'08' => 'август',
				'9' => 'сентябрь',
				'09' => 'сентябрь',
				'10' => 'октябрь',
				'11' => 'ноябрь',
				'12' => 'декабрь',
		);
		return $monthes[$number];
	}
	
	public static function isDate($date){
		$dateArray = explode('-',$date);
		//2014-01-06
		if(isset($dateArray[0]) && isset($dateArray[1]) && isset($dateArray[2])){
			if(checkdate($dateArray[01], $dateArray[2], $dateArray[0])){
				return true;
			}
		}
		return false;
	}
	
	public static function ratingToStars($number){
		$rating = array();
		switch ($number){
			case '-3': return array('star_active','star_active','star_active');
			case '-2': return array('star_active','star_active','star');
			case '-1': return array('star_active','star','star');
			case '0': return array('star','star','star');
			case '1': return array('star_active_green','star','star');
			case '2': return array('star_active_green','star_active_green','star');
			case '3': return array('star_active_green','star_active_green','star_active_green');
		}
	}
	
	public static function numberToString($number){
		switch($number){
			case 1: return 'Один';
			case 2: return 'Два';
			case 3: return 'Три';
			case 4: return 'Четыре';
			case 5: return 'Пять';
			case 6: return 'Шесть';
			case 7: return 'Семь';
			case 8: return 'Восемь';
			case 9: return 'Девять';
			case 11: return 'Одиннадцать';
			case 22: return 'Двадцать два';
			
			case 14: return 'Четырнадцать';
			case 15: return 'Пятнадцать';
			case 16: return 'Шестнадцать';
			case 17: return 'Семнадцать';
			case 18: return 'Восемнадцать';
			case 19: return 'Девятнадцать';
			case 20: return 'Двадцать';
			case 21: return 'Двадцать один';
			case 23: return 'Двадцать три';
			case 24: return 'Двадцать четыре';
			case 25: return 'Двадцать пять';
			case 26: return 'Двадцать шесть';
			case 27: return 'Двадцать семь';
			case 28: return 'Двадцать восемь';
			case 29: return 'Двадцать девять';
			case 30: return 'Тридцать';
			case 31: return 'Тридцать один';
			case 32: return 'Тридцать два';
			case 33: return 'Тридцать три';
			case 34: return 'Тридцать четыре';
			case 35: return 'Тридцать пять';
			case 36: return 'Тридцать шесть';
			case 37: return 'Тридцать семь';
			case 38: return 'Тридцать восемь';
			case 39: return 'Тридцать девять';
			case 40: return 'Сорок';
			case 41: return 'Сорок один';
			case 42: return 'Сорок два';
			case 43: return 'Сорок три';
			case 44: return 'Сорок четыре';
			case 45: return 'Сорок пять';
			case 46: return 'Сорок шесть';
			case 47: return 'Сорок семь';
			case 48: return 'Сорок восемь';
			case 49: return 'Сорок девять';
			case 50: return 'Пятьдесят';
			case 51: return 'Пятьдесят один';
			case 52: return 'Пятьдесят два';
			case 53: return 'Пятьдесят три';
			case 54: return 'Пятьдесят четыре';
			case 55: return 'Пятьдесят пять';
			case 56: return 'Пятьдесят шесть';
			case 57: return 'Пятьдесят семь';
			case 58: return 'Пятьдесят восемь';
			case 59: return 'Пятьдесят девять';
			case 60: return 'Шестьдесят';
			case 61: return 'Шестьдесят один';
			case 62: return 'Шестьдесят два';
			case 63: return 'Шестьдесят три';
			
		}
	}
	public static function commentTypeToRu($type){
		switch($type){
			case 'article': return 'Статьи';
			case 'news': return 'Новости';
			case 'magic': return 'Магия';
			case 'numerology': return 'Нумерология';
			case 'horoscope': return 'Гороскопы';
			case 'divination': return 'Гадания';
			case 'payservice': return 'П. гороскоп';
		}
	}
	public static function commentSubtypeToRu($type, $subtype){
		if($type == 'numerology' && $subtype == 'karma'){
			return 'Кармическая задача';
		}
		switch($subtype){
			case 'today': return 'Гороскоп на сегодня';
			case 'business-compability': return 'Бизнес совместимость';
			case 'love-compability': return 'Любовная совместимость';
			case 'simple': return 'Характеристика знака';
			case 'profession': return 'Гороскоп профессии';
			case 'karma': return 'Кармический';
			case 'health': return 'Гороскоп здоровья';
			case 'child': return 'Гороскоп ребенка';
			case 'business': return 'Бизнес гороскоп';
			case 'week': return 'Гороскоп на неделю';
			case 'month': return 'Гороскоп на месяц';
			case 'year': return 'Гороскоп на год';
			case 'lifepath': return 'Число жизненного пути';
			case 'self-expression': return 'Число самовыражения';
			case 'identity': return 'Число личности';
			case 'soul': return 'Число души';
			case 'achievement': return 'Важные годы жизни';
			case 'day': return 'Персональный день';
			case 'month': return 'Персональный месяц';
			case 'year': return 'Персональный год';
			case 'love': return 'Любовная совместимость';
			case 'partner': return 'Партнерская совместимость';
		}
	}
	public static function commentSignToRu($sign){
		switch($sign){
			case 'aries': return 'Овен';
			case 'taurus': return 'Телец';
			case 'gemini': return 'Близнецы';
			case 'cancer': return 'Рак';
			case 'leo': return 'Лев';
			case 'virgo': return 'Дева';
			case 'libra': return 'Весы';
			case 'scorpio': return 'Скорпион';
			case 'sagittarius': return 'Стрелец';
			case 'capricorn': return 'Козерог';
			case 'aquarius': return 'Водолей';
			case 'pisces': return 'Рыбы';
		}
	}
	
	public static function bannerToRu($banner){
		switch($banner){
			case 'top': return 'Заголовок';
			case 'bottom': return 'Подвал';
			case 'right1': return 'Справа №1';
			case 'right2': return 'Справа №2';
		}
	}
	public static function profileTypeToRu($type){
		switch($type){
			case 'sun': return 'Мой солнечный знак';
			case 'kelt': return 'Мой кельтcкий знак ';
			case 'china': return 'Мой восточный гороскоп';
			case 'karma': return 'Мой кармический гороскоп ';
			case 'number': return 'Число жизненного пути';
			case 'taro': return 'Мое Taрo личности';
		}
	}
	public static function ratingTypesToRu($type){
		switch($type){
			case 'taro': return 'Таро';
			case 'classic': return 'Карты';
			case 'rune': return 'Руны';
			case 'book': return 'Книга перемен';
			case 'other': return 'Другие гадания';
			case 'article': return 'Статьи';
			case 'magic': return 'Магия';
		}
	}
	
	public static function cleanUrlLastSlash($url){
		//TODO: отбрасывать слеш в конце урл
		$tokens = explode('/',$url);
		foreach($tokens as $index => $token){
			if(empty($token)){
				unset($tokens[$index]);
			}
		}
		return '/'.implode('/',$tokens);
		//var_dump(explode('/',$url)); die;
		//return $url;
	}
} 