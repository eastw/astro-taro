<?php
class ZC_View_Helper_PhoneElement extends Zend_View_Helper_FormElement{

	protected $html;
	
	public function phoneElement($name,$value = null, $attribs = null){
		
		$areanum = $geonum = $localnum = '';
		
		$helper = new Zend_View_Helper_FormText();
		$helper->setView($this->view);
		
		/*
		if(is_array($value)){
			$areanum = (isset($value['areanum'])) ? $value['areanum'] : '';
			$geonum = (isset($value['geonum'])) ? $value['geonum'] : '';
			$localnum = (isset($value['localnum'])) ? $value['localnum'] : '';
		}
		*/
		
		$this->html .= $helper->formText($name . '[areanum]','',array('size' => '3', 'maxlength' => '3'));
		$this->html .= $helper->formText($name . '[geonum]','',array('size' => '3', 'maxlength' => '3'));
		$this->html .= $helper->formText($name . '[localnum]','',array('size' => '3', 'maxlength' => '3'));
		
		return $this->html;
	} 
}