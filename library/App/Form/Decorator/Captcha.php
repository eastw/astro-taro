<?php 
class App_Form_Decorator_Captcha extends Zend_Form_Decorator_Captcha{
	
	public function render($content) {
		
		$element = $this->getElement();
		if (!method_exists($element, 'getCaptcha')) {
			return $content;
		}
	
		$view = $element->getView();
		if (null === $view) {
			return $content;
		}
	
		$name = $element->getFullyQualifiedName();
	
		$hiddenName = $name . '[id]';
		$textName = $name . '[input]';
	
		$label = $element->getDecorator("Label");
		if ($label) {
			$label->setOption("id", $element->getId() . "-input");
		}
	
		$placement = $this->getPlacement();
		$separator = $this->getSeparator();
	
		$captcha = $element->getCaptcha();
		$markup = $captcha->render($view, $element);
		$hidden = $view->formHidden($hiddenName, $element->getValue(), $element->getAttribs());
		$text = $view->formText($textName, '', $element->getAttribs());
	
	
		// CHANGE THE ORDER OF ELEMENTS AND ADD THE div AND span TAGS.
		switch ($placement) {
			case 'PREPEND':
				$content = '<div><span>' . $text . '</div></span>' .
						'<div><span>' . $markup . $hidden . '</div></span>' .
						$separator . $content;
						break;
			case 'APPEND':
			default:
				/*
				$content = $content . $separator .
				'<div><span>' . $text . '</div></span>' .
				'<div><span>' . $markup . $hidden . '</div></span>';
				*/
				$content = '<div class="cap_con_dash">';
				$content .= '<div id="'.(($name == 'captcha')?'simple-image-div':'full-image-div').'">'.$markup.$hidden.'</div>';
				$content .= '<img id="'.(($name == 'captcha')?'simple-refresh':'full-refresh').'" class="captcha_send" src="/files/images/captcha_send.png" alt="" />';
				$content .= '</div>';
				$content .= '<div class="clear"></div>';
				$content .= '<span>Введите слово с картинки:</span><br />';
				$content .= '<input id="'.$name.'-input" type="text" class="reg_form2" name="'.$name.'[input]" />';
				$content .= '<br/>';
		}
		return $content;
	}
}