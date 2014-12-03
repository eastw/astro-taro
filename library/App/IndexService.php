<?php
class App_IndexService{
	
	protected $moonService;
	protected $articleService;
	protected $numerologyService;
	protected $bannerService;
	protected $horoscopeService;
	protected $tagService;
	protected $divinationService;
	protected $deckService;
	
	const TARO_ETALON_DIVINATION = 30;
	const TARO_ETALON_DECK = 8;
	const RUNE_ETALON_DIVINATION = 31;
	const RUNE_ETALON_DECK = 13;
	const HEXAGRAMM_ETALON_DIVNATION = 26;
	const HEXAGRAMM_ETALON_DECK = 'kniga-peremen';
	
	public function __construct(){
		$this->moonService = new App_MoonService();
		$this->articleService = new App_ArticleService();
		$this->numerologyService = new App_NumerologyService();
		$this->horoscopeService = new App_HoroscopeService();
		$this->tagService = new App_TagService();
		$this->divinationService = new App_DivinationService();
		$this->deckService = new App_DeckService();
	}
	
	public function getTodayMoonData(){
		$data = $this->moonService->getDateSmallData(date('Y-m-d'));
		if(isset($data['moonDays']) && count($data['moonDays'])){
			foreach ($data['moonDays'][0]['attributes'] as $index => $attribute){
				$data['moonDays'][0]['attributes'][$index]['view_rating'] = App_UtilsService::ratingToStars($attribute['rating']);
			}
			$date = new Zend_Date(date('Y-m-d'));
			$data['today_long'] =$date->toString(Zend_Date::DATE_LONG);
			$data['second_day_time'] = '';
			if(count($data['moonDays']) > 1){
				$data['second_day_time'] = $data['moonDays'][1]['day_start'];
				 
			}
			if(isset($data['in_signs']) && count($data['in_signs']) > 1){
				$data['second_sign_time'] = $data['in_signs'][1]['signstart']; 
			}
			$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$data['moonDays'][0]['day_detail']['description']));
			$data['moonDays'][0]['day_detail']['short_day_desc'] = iconv('windows-1251','utf-8',substr($tmp, 0,130));
		}
		/*
		echo '<pre>';
		var_dump($data); die;
		*/
		return $data;
	}
	
	public function getMagicData(){
		$data = $this->articleService->getLastMagic();
		if(count($data)){
			foreach($data as $index => $item){
				$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$item['anonse']));
				$data[$index]['short_desc'] = iconv('windows-1251','utf-8',substr($tmp, 0,66));
				
				$ids_array = explode(';',$item['quicktag']);
				if(isset($ids_array[0]) && !empty($ids_array[0])){
					$data[$index]['tag_id'] = $ids_array[0];
				}else{
					// no tag id
					$data[$index]['tag_id'] = 17;
				}
			}
			
			$tags = $this->tagService->getCachedMagicTags();
			foreach($data as $index => $magic){
				foreach($tags as $tag){
					if($magic['tag_id'] == $tag['id']){
						$data[$index]['tag_data'] = $tag;
					}
				}
			}
		}
		/*
		echo '<pre>';
		var_dump($data); die;
		*/
		return $data;
	}
	
	public function getArticleData(){
		$data = $this->articleService->getLastArticles();
		if(count($data)){
			foreach($data as $index => $item){
				$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$item['anonse']));
				$data[$index]['short_desc'] = iconv('windows-1251','utf-8',substr($tmp, 0,66));
		
				$ids_array = explode(';',$item['quicktag']);
				if(isset($ids_array[0]) && !empty($ids_array[0])){
					$data[$index]['tag_id'] = $ids_array[0];
				}else{
					// no tag id
					$data[$index]['tag_id'] = 17;
				}
			}
				
			$tags = $this->tagService->getCachedTags();
			foreach($data as $index => $article){
				foreach($tags as $tag){
					if($article['tag_id'] == $tag['id']){
						$data[$index]['tag_data'] = $tag;
					}
				}
			}
			$result['left'] = array();
			$result['right'] = array();
			foreach($data as $index => $item){
				if($index < 5){
					$result['left'][] = $item;
				}else{
					$result['right'][] = $item;
				}
				unset($data[$index]);
			}
		}
		
		return $result;
	}
	
	public function getNewsData(){
		$data = $this->articleService->getLastNews();
		if(count($data)){
			foreach($data as $index => $item){
				$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$item['anonse']));
				$data[$index]['short_desc'] = iconv('windows-1251','utf-8',substr($tmp, 0,66));
		
				$ids_array = explode(';',$item['quicktag']);
				if(isset($ids_array[0]) && !empty($ids_array[0])){
					$data[$index]['tag_id'] = $ids_array[0];
				}else{
					// no tag id
					$data[$index]['tag_id'] = 17;
				}
			}
		
			$tags = $this->tagService->getCachedNewsTags();
			foreach($data as $index => $news){
				foreach($tags as $tag){
					if($news['tag_id'] == $tag['id']){
						$data[$index]['tag_data'] = $tag;
					}
				}
			}
		}
		
		return $data;
	}
	
	public function todayHoroscopeData(){
		$data = $this->horoscopeService->getShortSunSignsToday();
		if(count($data)){
			foreach($data as $index=>$item){
				$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$item['description']));
				$data[$index]['short_desc'] = iconv('windows-1251','utf-8',substr($tmp, 0,220));
			}
		}
		return $data;
	}
	
	public function taroDay($taroDay,$taroDayState){
		$data = $this->divinationService->getDivinationDataItemByPosition($taroDay-1, self::TARO_ETALON_DIVINATION);
		$deck = $this->deckService->getDeckById(self::TARO_ETALON_DECK);
		
		$tmp = array();
		$card = '';
		if($taroDayState){
			$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$data['description']));
			$card = '.png';
			$data['taroTitle'] = $data['title'];
		}else{
			$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$data['description_reverse']));
			$card = '_0.png';
			$data['taroTitle'] = $data['title_reverse'];
		}
		$data['state'] = $taroDayState;
		//var_dump($taroDayState); die;
		$data['taroDay'] = iconv('windows-1251','utf-8',substr($tmp, 0,357));
		$data['taroDayImage'] = '/files/decks/'.$deck['folder_alias'].'/'.$data['deck_position'].$card;
		
		return $data;
	}
	
	public function runeDay($runeDay,$runeDayState){
		//var_dump($runeDayState); die;
		$data = $this->divinationService->getDivinationDataItemByPosition($runeDay-1, self::RUNE_ETALON_DIVINATION);
		$deck = $this->deckService->getDeckById(self::RUNE_ETALON_DECK);
		
		$tmp = array();
		$card = '';
		if($runeDayState){
			$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$data['description']));
			$card = '.png';
			$data['runeTitle'] = $data['title'];
		}else{
			//echo 'reverce'; die;
			$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$data['description_reverse']));
			$card = '_0.png';
			$data['runeTitle'] = $data['title_reverse'];
		}
		$data['state'] = $runeDayState;
		$data['runeDay'] = iconv('windows-1251','utf-8',substr($tmp, 0,360));
		$data['runeDayImage'] = '/files/decks/'.$deck['folder_alias'].'/'.$data['deck_position'].$card;
		return $data;
	}
	
	public function hexagrammDay($hexagrammDay){
		$data = $this->divinationService->getBookDescriptionItem(self::HEXAGRAMM_ETALON_DIVNATION,$hexagrammDay);
		$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$data['description']));
		$data['hexagrammDay'] = iconv('windows-1251','utf-8',substr($tmp, 0,360));
		$data['hexagrammTitle'] = $data['title'];
		$data['hexagrammDayImage'] = '/files/decks/'.self::HEXAGRAMM_ETALON_DECK.'/'.$data['image'];
		return $data;
	}
	
	public function numberDayData($birthday){
		$data = $this->numerologyService->calcTodayNumber($birthday);
		//var_dump($birthday); die;
		$tmp = iconv('utf-8','windows-1251',preg_replace('/<\/?[^>]+>/ims','',$data['description']));
		$data['short_desc'] = iconv('windows-1251','utf-8',substr($tmp, 0,400));
		//var_dump($data); die;
		return $data;
	}
	/*
	public function payService($data,$email){
		$mailService = new App_MailService();
		$mailService->sendPayServiceMail($data,$email);
	}
	*/
	
}