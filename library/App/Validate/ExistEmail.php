<?php
class App_Validate_ExistEmail extends Zend_Validate_Abstract
{
    const EMAIL_EXISTS='';
    
    protected $_messageTemplates = array(
        self::EMAIL_EXISTS=>'Пользователь с почтой "%value%" уже существует'
    );
    public function __construct()
    {
         $this->users = new Application_Model_DbTable_Users();;
    }
    
    public function isValid($value, $context=null)
    {
        $this->_setValue($value);
        //insert logic to check here?
        $query = $this->users->select()->from('users')
        ->where($this->users->getAdapter()->quoteInto('email=?', strtolower($value)));

		$stmt = $query->query(Zend_Db::FETCH_ASSOC);
        $result = $stmt->fetchAll();//fetchRow();
        //var_dump($result); die;
        if (count($result)){
        	$this->_error(self::EMAIL_EXISTS);
            return false;
        }
        return true;
    }
}
