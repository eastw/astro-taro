<?php
class Application_Form_HoroscopeKarmaPeriodForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/karma-period-form.phtml')),
		));
		
		//$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		
		//$this->setName('karma-period');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$startdate = new Zend_Form_Element_Text('startdate');
		$startdate->setLabel('Начало периода')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators)
				->setAttrib('style', 'width:100px;');
		
		$enddate = new Zend_Form_Element_Text('enddate');
		$enddate->setLabel('Окончание периода')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators)
				->setAttrib('style', 'width:100px;');
		
		$retrograde = new Zend_Form_Element_Select('is_retrograd');
		$retrograde->setLabel('Ретроградность')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators)
				->addMultiOptions(array(
					'n' => 'Нет',
					'y' => 'Да',
				));
				
		$sign = new Zend_Form_Element_Select('sign');
		$sign->setLabel('Знак, который проходит Сатурн в этом периоде')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);
				
		$desc = new Zend_Form_Element_Textarea('desc');
		$desc->setLabel('Oписание периода')
					->setRequired(true)
					->addFilter('StringTrim')
					->addValidator('NotEmpty')
					->setDecorators($decorators);
		
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
		$cancel->setAttrib('onclick','window.location.href="/admin/horoscope/by-karma"');
		
		$this->addElements(array($startdate,$enddate,$retrograde,$sign,$desc,$submit, $cancel,$page));
		
	}
	
	
	public function fillSigns($data){
		$sign = $this->getElement('sign');
		$sign->addMultiOptions(array(
				'' => 'Выберите знак',
		));
		if(count($data)){
			foreach($data as $item){
				$sign->addMultiOptions(array(
						$item['id'] => $item['sign_ru'],
				));
			}
		}
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
				$decks->addMultiOptions(array(
						$item['id'] => $item['name'],
				));
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
}