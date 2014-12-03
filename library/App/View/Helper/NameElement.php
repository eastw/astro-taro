<?php
class App_View_Helper_NameElement extends Zend_View_Helper_FormElement{

	protected $html;
	
	public function nameElement($name,$value = null, $attribs = null){
		
		$fname = $mname = $lname = '';
		
		$helper = new Zend_View_Helper_FormText();
		$helper->setView($this->view);
		
		if (is_array($value))
		{
			$fname = (isset($value['fname'])) ? $value['fname'] : '';
			$mname = (isset($value['mname'])) ? $value['mname'] : '';
			$lname = (isset($value['lname'])) ? $value['lname'] : '';
		}
		
		$this->html .= $helper->formText($name . '[fname]','',array('size' => '3', 'maxlength' => '3'));
		$this->html .= $helper->formText($name . '[mname]','',array('size' => '3', 'maxlength' => '3'));
		$this->html .= $helper->formText($name . '[lname]','',array('size' => '3', 'maxlength' => '3'));
		
		return $this->html;
	} 
}