<?php
class Application_Form_DeckForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/deck-form.phtml')),
		));
		
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		//$this->setIsArray(true);
		
		$this->setName('deck');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$imgDecorators = array(
						'File',
						'Errors',
						array(array('data'=>'HtmlTag'),array('tag'=>'td')),
						array('Label',array('tag'=>'td')),
						array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
				);
		$noteDecorators = array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
						array('Errors'),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
					);
		
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Название колоды')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);
		
		$back = new Zend_Form_Element_File('back');
		$back->setLabel('Рубашка колоды')
				->addValidator('Size', false, 1024000)
				->addValidator('Extension', false, 'jpg,png,gif')
				->setDecorators($imgDecorators);
		
		$backNote = new App_Form_Element_InfoLabel(
				'back_note',
				array('value' => '')
		);
		$backNote->setDecorators($noteDecorators);
		
		$reshuffle = new Zend_Form_Element_File('reshuffle');
		$reshuffle->setLabel('Перетасовка')
				->addValidator('Size', false, 12024000)
				->addValidator('Extension', false, 'gif')
				->setDecorators($imgDecorators);
		
		$reshuffleNote = new App_Form_Element_InfoLabel(
				'reshuffle_note',
				array('value' => '')
		);
		$reshuffleNote->setDecorators($noteDecorators);
		
		/*
		$removal = new Zend_Form_Element_File('removal');
		$removal->setLabel('Снятие карт')
				->addValidator('Size', false, 12024000)
				->addValidator('Extension', false, 'gif')
				->setDecorators($imgDecorators);
		
		$removalNote = new App_Form_Element_InfoLabel(
				'removal_note',
				array('value' => '')
		);
		$removalNote->setDecorators($noteDecorators);
		*/
		
		/*
		$activity = new Zend_Form_Element_Select('activity');
		$activity->setLabel('Активность на сайте')
		->setDecorators($decorators)
		->setMultiOptions(array(
			'y' => 'Активна',
			'n' => 'Неактивна',
		));
		*/
		
		$type = new Zend_Form_Element_Select('type');
		$type->setLabel('Тип колоды')
			->setDecorators($decorators)
			->setRegisterInArrayValidator(false);
		
		
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
		$cancel->setAttrib('onclick','window.location.href="/admin/divination/deck"');
		
		//$this->setAttrib('class', 'article-form');
		/*
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'table')),
				'Form',
		));
		*/
		$this->addElements(array($title,$type,$back,$backNote,$reshuffle,$reshuffleNote,/*$removal,$removalNote,*/$submit, $cancel,$page));
		
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
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
	public function fillTags($data){
		$tags = $this->getElement('tagslist');
		$tags->addMultiOptions(array(
				'' => 'Выберите тег',
		));
		if(count($data)){
			foreach($data as $item){
				$tags->addMultiOptions(array(
						$item['id'] => $item['tagname'],
				));
			}
		}
	}
	
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
}