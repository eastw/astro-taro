<?php

class App_DreamService {

    protected $dreamWord;
    protected $dreamType;
    protected $dreamWordDescription;

    private static $instance = null;

    protected $dreamPagesCacheName;

    private function __construct()
    {
        $this->dreamWord = new Application_Model_DbTable_DreamWordTable();
        $this->dreamType = new Application_Model_DbTable_DreamTypeTable();
        $this->dreamWordDescription = new Application_Model_DbTable_DreamWordDescriptionTable();

        $this->dreamPagesCacheName = str_replace('.','_', $_SERVER['HTTP_HOST']) . '_dream_pages';
    }

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new App_DreamService();
        }
        return self::$instance;
    }

    public function listWordQuery(){
        return $this->dreamWord->getAdapter()->select()->from('dream_word')->order('id DESC');
    }

    public function listTypeQuery(){
        return $this->dreamType->getAdapter()->select()->from('dream_type')->order('id DESC');
    }

    public function addWord($data){
        $insertData = array(
            'word' => $data['word'],
            'alias' => App_UtilsService::generateTranslit($data['word']),
            'title' => $data['title'],
            'keywords' => $data['keywords'],
            'description' => $data['seodescription']
        );
        $this->dreamWord->insert($insertData);
    }

    public function getWordById($id){
        $query = $this->dreamWord->getAdapter()->select();
        $query->from(array('dream_word'))
            ->where($this->dreamWord->getAdapter()->quoteInto('id=?',$id));
        $stm = $query->query();
        return $stm->fetch();
    }

    public function getTypeById($id){
        $query = $this->dreamType->getAdapter()->select();
        $query->from(array('dream_type'))
            ->where($this->dreamType->getAdapter()->quoteInto('id=?',$id));
        $stm = $query->query();
        return $stm->fetch();
    }

    public function updateWord($data, $id){
        $updateData = array(
            'word' => $data['word'],
            'alias' => App_UtilsService::generateTranslit($data['word']),
            'title' => $data['title'],
            'keywords' => $data['keywords'],
            'description' => $data['seodescription']
        );
        $this->dreamWord->update($updateData,$this->dreamWord->getAdapter()->quoteInto('id=?',$id));
    }

    public function removeWord($id){
        $this->dreamWord->delete($this->dreamWord->getAdapter()->quoteInto('id=?',$id));
    }

    public function searchWord($squery){
        $query =$this->dreamWord->getAdapter()->select();
        if(!empty($squery)){
            $query->from(array('dream_word'))
                ->where('word LIKE \'%'.$squery.'%\' ')->order('id DESC');
        }else{
            $query->from('dream_word')->order('id DESC');
        }
        $stm = $query->query();
        return $stm->fetchAll();
    }

    public function addType($data){
        $insertData = array(
            'name' => $data['name'],
            'alias' => App_UtilsService::generateTranslit($data['name']),
            'description' => $data['description'],
            'title' => $data['title'],
            'keywords' => $data['keywords'],
            'seodescription' => $data['seodescription']
        );
        $this->dreamType->insert($insertData);
    }

    public function updateType($data, $id){
        $updateData = array(
            'name' => $data['name'],
            'alias' => App_UtilsService::generateTranslit($data['name']),
            'description' => $data['description'],
            'title' => $data['title'],
            'keywords' => $data['keywords'],
            'seodescription' => $data['seodescription']
        );
        $this->dreamType->update($updateData,$this->dreamType->getAdapter()->quoteInto('id=?',$id));
    }

    public function removeType($id){
        $this->dreamType->delete($this->dreamType->getAdapter()->quoteInto('id=?',$id));
    }

    public function getWords(){
        $words = $this->dreamWord->fetchAll(true);
        if($words){
            $words = $words->toArray();
        }
        return $words;
    }
    public function getTypes(){
        $types = $this->dreamType->fetchAll(true);
        if($types){
            $types = $types->toArray();
        }
        return $types;
    }

    public function getDescriptionByWordAndType($wordId, $typeId){
        $query = $this->dreamWordDescription->select();
        $adapter = $this->dreamWordDescription->getAdapter();
        $query->from(array('dream_word_description'),array('description'))
            ->where($adapter->quoteInto('word_id = ?', $wordId))
            ->where($adapter->quoteInto('type_id = ?', $typeId));
        $stm = $query->query();
        return array(
            'status' => 'success',
            'data' => $stm->fetch()
        );
    }

    public function saveDescriptionByWordAndType($wordId, $typeId, $description){
        $query = $this->dreamWordDescription->select();
        $adapter = $this->dreamWordDescription->getAdapter();
        $query->from(array('dream_word_description'),array('id','description'))
            ->where($adapter->quoteInto('word_id = ?', $wordId))
            ->where($adapter->quoteInto('type_id = ?', $typeId));
        $stm = $query->query();
        $data = $stm->fetch();
        if(!$data){
            $insertData = array(
                'word_id' => $wordId,
                'type_id' => $typeId,
                'description' => $description
            );
            $this->dreamWordDescription->insert($insertData);
        }else{
            $updateData = array(
                'description' => $description
            );
            $this->dreamWordDescription->update($updateData, $adapter->quoteInto('id = ?',$data['id']));
        }
        return array('status' => 'success');
    }

    public function getWordAutocomplete($squery){
        $query =$this->dreamWord->getAdapter()->select();
        if(!empty($squery)){
            $query->from(array('dream_word'),array('id','value'=>'word'))
                ->where('word LIKE \'%'.$squery.'%\' ')->order('id DESC');
        }else{
            $query->from('dream_word')->order('id DESC');
        }
        $stm = $query->query();
        return $stm->fetchAll();
    }

    /*Pages*/
    public function listPagesQuery(){
        $query = $this->horoscopePages->select();
        $query->from('horoscope_pages')->order('id desc');
        return $query;
    }

    public function addPage($data){
        $insertData = array(
            'horoscope_type' => $data['page_type'],
            'name_ru' => $data['name_ru'],
            'title' => $data['title'],
            'keywords' => $data['seokeywords'],
            'description' => $data['seodescription'],
            'minidesc' => $data['minidesc'],
        );
        $this->horoscopePages->insert($insertData);
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->horoscopePagesCacheName);
    }

    public function savePage($data,$id){
        $updateData = array(
            'horoscope_type' => $data['page_type'],
            'name_ru' => $data['name_ru'],
            'title' => $data['title'],
            'keywords' => $data['seokeywords'],
            'description' => $data['seodescription'],
            'minidesc' => $data['minidesc'],
        );
        $this->horoscopePages->update($updateData,$this->horoscopePages->getAdapter()->quoteInto('id=?', $id));
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->horoscopePagesCacheName);
    }

    public function deletePage($id){
        $this->horoscopePages->delete($this->horoscopePages->getAdapter()->quoteInto('id=?', $id));
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->horoscopePagesCacheName);
    }
}