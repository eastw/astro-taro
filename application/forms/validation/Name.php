<?php
class Application_Form_Validation_Name extends Zend_Validate_Abstract{

	const STRING_EMPTY = "stringEmpty";

	protected $_messageTemplates = array(
			self::STRING_EMPTY => 'Ни одно из полей не должно быть пустым'
	);

	public function isValid($value){
		$this->_setValue((string)$value);
		return true;
	}
}