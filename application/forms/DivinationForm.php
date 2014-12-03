<?php
class Application_Form_DivinationForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/divination-form.phtml')),
		));
		
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		
		$this->setName('divination');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Заголовок гадания')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);
		
		$desc = new Zend_Form_Element_Textarea('desc');
		$desc->setLabel('Короткое описание гадания')
					->setRequired(true)
					->addFilter('StringTrim')
					->addValidator('NotEmpty')
					->setDecorators($decorators);
		
		$type = new Zend_Form_Element_Select('type');
		$type->setLabel('Тип гадания')
				->setRequired(true)
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->setRegisterInArrayValidator(false)
				->setDecorators($decorators);
		
		$category = new Zend_Form_Element_Select('category');
		$category->setLabel('Категория гадания')
					->setRequired(true)
					->addFilter('StringTrim')
					->addValidator('NotEmpty')
					->setRegisterInArrayValidator(false)
					->setDecorators($decorators);
		
		//добавляется только при редактировании
		/*
		$activity = new Zend_Form_Element_Select('activity');
		$activity->setLabel('Активность на сайте')
				->setDecorators($decorators)
				->setMultiOptions(array(
					'n' => 'Неактивна',
					'y' => 'Активна',
				));
		*/
		
		$onlyOldArkans = new Zend_Form_Element_Select('only_old_arkans');
		$onlyOldArkans->setLabel('В гадании участвуют')
					->setRequired(true)
					->addFilter('StringTrim')
					->addValidator('NotEmpty')
					->setDecorators($decorators)
					->addMultiOptions(array(
						'y' => 'Только старшие Арканы',
						'n' => 'Вся колода'	
					));
		
		$keywords = new Zend_Form_Element_Text('seokeywords');
		$keywords->setLabel('SEO ключевые слова')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setDecorators($decorators);
		
		$seodesc = new Zend_Form_Element_Textarea('seodescription');
		$seodesc->setLabel('SEO описание')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setDecorators($decorators);
		
		$decks = new Zend_Form_Element_Select('decks');
		$decks->setLabel('Колоды')
					->addValidator('NotEmpty')
					->setRegisterInArrayValidator(false)
					->setDecorators(array(
						'ViewHelper',
					));
		
		$adddeck = new Zend_Form_Element_Button('adddeck');
		$adddeck->setLabel('Добавить колоду');
		$adddeck->setDecorators(array(
				'ViewHelper',
		));
		
		$decksplace = new App_Form_Element_InfoLabel(
				'decksplace',
				array('value' => '<div id="decksplace"></div>')
		);
		$decksplace->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
				array('Errors'),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));
		
		$image = new Zend_Form_Element_File('image');
		$image->setLabel('Фон гадания')
				->setRequired(true)
				->addValidator('Size', false, 10024000)
				->addValidator('Extension', false, 'jpg,png,gif')
				->setDecorators(
						array(
								'File',
								'Errors',
								array(array('data'=>'HtmlTag'),array('tag'=>'td')),
								array('Label',array('tag'=>'td')),
								array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
						));
		
		$imgNote = new App_Form_Element_InfoLabel(
				'img_note',
				array('value' => '')
		);
		$imgNote->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
				array('Errors'),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));
		
		$image2 = new Zend_Form_Element_File('image2');
		$image2->setLabel('Картинка в листинге')
				->setRequired(true)
				->addValidator('Size', false, 10024000)
				->addValidator('Extension', false, 'jpg,png,gif')
				->setDecorators(
						array(
								'File',
								'Errors',
								array(array('data'=>'HtmlTag'),array('tag'=>'td')),
								array('Label',array('tag'=>'td')),
								array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
						));
		
		$imgNote2 = new App_Form_Element_InfoLabel(
				'img_note2',
				array('value' => '')
		);
		$imgNote2->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
				array('Errors'),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));
		
		$alignment = new Zend_Form_Element_File('alignment_form');
		$alignment->setLabel('Форма расклада')
				->setRequired(true)
				->addValidator('Size', false, 10024000)
				->addValidator('Extension', false, 'jpg,png,gif')
				->setDecorators(
						array(
								'File',
								'Errors',
								array(array('data'=>'HtmlTag'),array('tag'=>'td')),
								array('Label',array('tag'=>'td')),
								array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
						));
		
		$imgNote3 = new App_Form_Element_InfoLabel(
				'img_note3',
				array('value' => '')
		);
		$imgNote3->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
				array('Errors'),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));
		
		
		
		$front_background = new Zend_Form_Element_File('front_background');
		$front_background->setLabel('Мини фон гадания')
				->addValidator('Size', false, 10024000)
				->addValidator('Extension', false, 'jpg,png,gif')
				->setDecorators(
						array(
								'File',
								'Errors',
								array(array('data'=>'HtmlTag'),array('tag'=>'td')),
								array('Label',array('tag'=>'td')),
								array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
						));
		
		$imgNote4 = new App_Form_Element_InfoLabel(
				'img_note4',
				array('value' => '')
		);
		$imgNote4->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
				array('Errors'),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));
		
		$cards = new Zend_Form_Element_Select('cards');
		$cards->setLabel('Количество карт в раскладе (вместе с сигнификатором)')
				->setRequired(true)
				->addValidator('NotEmpty')
				->setDecorators($decorators);
				/*
				->addMultiOptions(array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
						'13' => '13',
						'14' => '14',
						'15' => '15',
						'16' => '16',
						'17' => '17',
				));
				*/
		$signs = new Zend_Form_Element_Select('significators');
		$signs->setLabel('Количество сигнификаторов')
				->setRequired(true)
				->addValidator('NotEmpty')
				->setDecorators($decorators)
				->addMultiOptions(array(
						'0' => 'Нет сигнификатора',
						'1' => 'Один',
						'2' => 'Два',
				));
		/*		
		$useSign = new Zend_Form_Element_Select('use_sign');
		$useSign->setLabel('Вывод сигнификатора (только для классики)')
				->setRequired(true)
				->addValidator('NotEmpty')
				->setDecorators($decorators)
				->addMultiOptions(array(
						'n' => 'Нет',
						'y' => 'Да',
				));
		*/		
		$page = new Zend_Form_Element_Hidden('page');
		$page->setDecorators(array(
				'ViewHelper',
		));
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Сохранить');
		$submit->setDecorators(array(
				'ViewHelper',
		));
		
		$cancel = new Zend_Form_Element_Button('cancel');
		$cancel->setLabel('Отмена');
		
		$cancel->setDecorators(array(
				'ViewHelper',
		));
		$cancel->setAttrib('onclick','window.location.href="/admin/divination/taro"');
		
		
		
		$this->addElements(array($title,$desc,$image,$imgNote,$image2,$imgNote2,$alignment,$imgNote3,$front_background,$imgNote4,$category,$type,$onlyOldArkans,/*$activity,*/$decks,$adddeck,$decksplace,$keywords,$seodesc,$cards,$signs/*,$useSign*/,$submit, $cancel,$page));
		$this->fillCardsInAlignmnt();
	}
	
	public function fillDecks($data){
		//echo '<pre>';
		//var_dump($data); die;
		$decks = $this->getElement('decks');
		$decks->addMultiOptions(array(
				'' => 'Выберите колоду',
		));
		if(count($data)){
			foreach($data as $item){
				if($item['activity'] == 'y'){
					$decks->addMultiOptions(array(
							$item['id'] => $item['name'],
					));
				}
			}
		}
	}
	
	public function fillCategories($data){
		$category = $this->getElement('category');
		$category->setMultiOptions(array());
		$category->addMultiOptions(array(
				'' => 'Выберите категорию',
		));
		if(count($data)){
			foreach($data as $item){
				$category->addMultiOptions(array(
						//$item['id'] => $item['category'],
						$item['id'] => $item['name'],
				));
			}
		}
	}
	public function fillTypes($data){
		$type = $this->getElement('type');
		if(count($data)){
			foreach($data as $item){
				$type->addMultiOptions(array(
						$item['id'] => $item['name'],
				));
			}
		}
	}
	/*
	public function fillTagsplace(){
		$session = new Zend_Session_Namespace('addtag');
		$html = '';
		$disabled_options = array();
		foreach($session->tags as $index => $tag){
			$html .= '<div id="tagplace_'.$index.'" class="tag"><a>'.$tag.'</a><img src="/files/images/input_clear.gif" onclick="delete_tag(\''.$index.'\')"></div>';
			$disabled_options[] = $index;
		}
		$html = '<div id="tagsplace">'.$html.'</div>';
		$this->tagsplace->setValue($html);
		$this->tagslist->setAttrib('disable',$disabled_options);
	}
	*/
	
	public function fillDecksplace(){
		$session = new Zend_Session_Namespace('adddeck');
		$html = '';
		$disabled_options = array();
		if(isset($session->decks) && count($session->decks)){
			foreach($session->decks as $index => $deck){
				$html .= '<div id="deckplace_'.$index.'" class="tag"><a>'.$deck.'</a><img src="/files/images/input_clear.gif" onclick="delete_deck(\''.$index.'\')"></div>';
				$disabled_options[] = $index;
			}
		}
		$html = '<div id="decksplace">'.$html.'</div>';
		$this->decksplace->setValue($html);
		$this->decks->setAttrib('disable',$disabled_options);
	}
	
	protected function fillCardsInAlignmnt(){
		$cards = $this->getElement('cards');
		for($i = 0,$n=45; $i < $n;$i++){
			$cards->addMultiOptions(array(
					($i + 1 ) => ($i+1),
			));
		}
	}
}