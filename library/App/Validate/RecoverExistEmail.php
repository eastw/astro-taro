<?php
class App_Validate_RecoverExistEmail extends Zend_Validate_Abstract
{
    const EMAIL_NOT_EXISTS = '';
    
    protected $_messageTemplates = array(
        self::EMAIL_NOT_EXISTS=>'Пользователя с почтой "%value%" не существует'
    );
    public function __construct()
    {
         $this->users = new Application_Model_DbTable_Users();
    }
    
    public function isValid($value, $context=null)
    {
        $this->_setValue($value);
        //insert logic to check here?
        $query = $this->users->select()->from('users')
        	->where($this->users->getAdapter()->quoteInto('email=?', strtolower($value)));

		$stmt = $query->query(Zend_Db::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        
        if (count($result)){
            return true;
        }
        $this->_error(self::EMAIL_NOT_EXISTS);
        return false;
    }
}
