<?php
class App_Validate_ExistWord extends Zend_Validate_Abstract
{
    const WORD_EXISTS='';
    
    protected $_messageTemplates = array(
        self::WORD_EXISTS=>'Слово "%value%" уже существует'
    );
    public function __construct()
    {
         $this->word = new Application_Model_DbTable_DreamWordTable();
    }
    
    public function isValid($value, $context=null)
    {
        $this->_setValue($value);
        //insert logic to check here?
        $query = $this->word->select()->from('dream_word')
        ->where($this->word->getAdapter()->quoteInto('word=?', $value));

		$stmt = $query->query(Zend_Db::FETCH_ASSOC);
        $result = $stmt->fetchAll();//fetchRow();
        //var_dump($result); die;
        if (count($result)){
        	$this->_error(self::WORD_EXISTS);
            return false;
        }
        return true;
    }
}
