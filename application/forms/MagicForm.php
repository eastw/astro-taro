<?php
class Application_Form_MagicForm extends Zend_Form{
	
	public function init(){
		
		$translate = new Zend_Translate('array',APPLICATION_PATH.'/languages/resources/');
		Zend_Registry::set('Zend_Translate',$translate);
		
		$this->clearDecorators();
		$this->setDecorators(array(
				'PrepareElements',
				array('ViewScript', array('viewScript' => 'partials/article-form.phtml')),
		));
		
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		//$this->setIsArray(true);
		
		$this->setName('article');
		$this->setMethod('post');
		
		$decorators = array(
				'ViewHelper',
				'Errors',
				array(array('data'=>'HtmlTag'),array('tag'=>'td')),
				array('Label',array('tag'=>'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
		);
		
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Заголовок')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->setValidators(array('NotEmpty'))
				->setDecorators($decorators);
		
		$image = new Zend_Form_Element_File('image');
		$image->setLabel('Картинка анонса')
		->addValidator('Size', false, 1024000)
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
		
		$anonse = new Zend_Form_Element_Textarea('anonse');
		$anonse->setLabel('Анонс')
					->setRequired(true)
					->addFilter('StripTags')
					->addFilter('StringTrim')
					->addValidator('NotEmpty')
					->setDecorators($decorators);
		
		$content = new Zend_Form_Element_Textarea('content');
		$content->setLabel('Содержимое статьи')
				->setRequired(true)
				->setDecorators($decorators);
		
		$activity = new Zend_Form_Element_Select('activity');
		$activity->setLabel('Активность на сайте')
		->setDecorators($decorators)
		->setMultiOptions(array(
			'y' => 'Активна',
			'n' => 'Неактивна',
		));
		
		$keywords = new Zend_Form_Element_Text('seokeywords');
		$keywords->setLabel('SEO ключевые слова')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setDecorators($decorators);
		
		$desc = new Zend_Form_Element_Textarea('seodescription');
		$desc->setLabel('SEO описание')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setDecorators($decorators);
		
		
		$tagslist = new Zend_Form_Element_Select('tagslist');
		$tagslist->setLabel('Теги')
				->setRegisterInArrayValidator(false)
				//->setDecorators($decorators);
				->setDecorators(array(
						'ViewHelper',
				));
		
		$addtag = new Zend_Form_Element_Button('addtag');
		$addtag->setLabel('Добавить тег');
		$addtag->setDecorators(array(
				'ViewHelper',
		));
		
		$tagsplace = new App_Form_Element_InfoLabel(
				'tagsplace',
				array('value' => '<div id="tagsplace"></div>')
		);
		$tagsplace->setDecorators(array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td','colspan' => '2')),
				array('Errors'),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));
		
		
		$page = new Zend_Form_Element_Hidden('page');
		$page->setDecorators(array(
				'ViewHelper',
		));
		
		/*
		$tags = new Zend_Form_Element_Hidden('tags');
		$tags->setIsArray(true);
		$tags->setDecorators(array(
				'ViewHelper',
		));
		*/
		
		
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
		$cancel->setAttrib('onclick','window.location.href="/admin/magic"');
		
		//$this->setAttrib('class', 'article-form');
		/*
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'table')),
				'Form',
		));
		*/
		$this->addElements(array($title,$image,$imgNote,$anonse,$content,$activity,$keywords,$desc,$tagslist,$addtag,$tagsplace,$submit, $cancel,$page/*,$tags*/));
		
		
		$this->addDisplayGroup(array('submit','cancel'),'buttons');
		
		$buttons = $this->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
				'FormElements',
				array('row'=>'HtmlTag', array('tag' => 'td','colspan' => '2','style' => 'text-align:right')),
		));
	}
	
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
		$session = new Zend_Session_Namespace('addmagictag');
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
}