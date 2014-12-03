<?php
class App_HtmlMailer extends Zend_Mail{

	static $fromName = 'Astrotarot';
	
	static $fromEmail = 'info.astrotarot@gmail.com';
	
	static $_defaultView;
	
	protected $_view;
	
	protected static function getDefaultView(){
		if(self::$_defaultView === null){
			self::$_defaultView = new Zend_View();
			self::$_defaultView->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/partials/mail');
		}
		return self::$_defaultView;
	}
	
	public function setViewParam($property,$value){
		$this->_view->__set($property,$value);
		return $this;
	}
	
	public function sendHtmlTemplate($template,$encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE){
		$html = $this->_view->render($template);
        $this->setBodyHtml($html,$this->getCharset(), $encoding);
        $this->send();
	}
	
	public function __construct($charset = 'UTF-8'){
		parent::__construct($charset);
		
		$this->setFrom(self::$fromEmail,self::$fromName);
		
		$this->_view = self::getDefaultView();
	}
}