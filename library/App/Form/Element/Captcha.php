<?php
class App_Form_Element_Captcha extends Zend_Form_Element_Captcha{
	
	public function render(Zend_View_Interface $view = null)     {
		$captcha  = $this->getCaptcha();
		
		$captcha->setName($this->getFullyQualifiedName());
	
		$decorators = $this->getDecorators();
	
		// BELOW IS WHERE THE NEW DECORATOR IS USED
		$decorator = new App_Form_Decorator_Captcha(array('captcha' => $captcha));
	
		array_unshift($decorators, $decorator);
	
		$decorator  = $captcha->getDecorator();
	
		$this->setDecorators($decorators);
	
	
		$this->setValue($this->getCaptcha()->generate());
	
		return Zend_Form_Element::render($view);
	}
}