<?php

class App_DreamService {

    protected $dreamWord;
    protected $dreamType;
    protected $dreamWordDescription;

    private static $instance = null;

    protected $typesCacheName;


    private function __construct()
    {
        $this->dreamWord = new Application_Model_DbTable_DreamWordTable();
        $this->dreamType = new Application_Model_DbTable_DreamTypeTable();
        $this->dreamWordDescription = new Application_Model_DbTable_DreamWordDescriptionTable();

        $host = str_replace('.','_', $_SERVER['HTTP_HOST']);
        $this->typesCacheName = $host . '_dream_types';
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
            'minidesc' => $data['minidesc'],
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
            'minidesc' => $data['minidesc'],
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
        Zend_Registry::get("cache")->remove($this->typesCacheName);
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
        Zend_Registry::get("cache")->remove($this->typesCacheName);
    }

    public function removeType($id){
        $this->dreamType->delete($this->dreamType->getAdapter()->quoteInto('id=?',$id));
        Zend_Registry::get("cache")->remove($this->typesCacheName);
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


    public static function getAlphabet(){
        return array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','К','Л','М','Н','О','П','Р','С','Т','У',
            'Ф','Х','Ц','Ч','Ш','Щ','Э','Ю','Я');
    }

    public function getAllTypes(){
        $cache = Zend_Registry::get("cache");
        if(!$types = $cache->load($this->typesCacheName, true)) {
            $types = $this->dreamType->fetchAll(true)->toArray();
            $cache->save($types, $this->typesCacheName);
        }
        return $types;
    }

    public function getWordsByLetter($letter){
        $letter = mb_strtoupper($letter, 'UTF-8');
        if(!in_array($letter, self::getAlphabet())){
            throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
        }
        $query = $this->dreamWord->select();
        $query->from('dream_word')->where("word LIKE '" . $letter . "%'", $letter)->order('word');
        $stm = $query->query();
        $rawWords = $stm->fetchAll();

        $totalCount = count($rawWords);
        $fullColumnCount = ceil($totalCount / 3);
        //$lastColumnCount = $totalCount - $fullColumnCount * 2;

        $result = array('first' => array(), 'second' => array(), 'third' => array());

        foreach($rawWords as $index => $word){
            if($index <= ($fullColumnCount-1)){
                $result['first'][] = $word;
            }
            if($index > ($fullColumnCount-1) && $index <= ((2 * $fullColumnCount) - 1)){
                $result['second'][] = $word;
            }
            if($index > ((2 * $fullColumnCount) - 1)){
                $result['third'][] = $word;
            }
        }
        return $result;
    }

    public function getTypeByAlias($alias){
        $types = $this->getAllTypes();
        $found = false;
        foreach($types as $type){
            if($type['alias'] == $alias){
                $found = true;
                break;
            }
        }
        if(!$found){
            throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
        }
        /*
        $query = $this->dreamType->select();
        $query->from('dream_type')->where($this->dreamType->getAdapter()->quoteInto('alias=?',$alias));
        $stm = $query->query();
        $type = $stm->fetch();
        if(!$type){
            throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
        }
        */
        return $type;
    }

    public function getWordByAlias($alias){
        $query = $this->dreamWord->select();
        $query->from('dream_word')->where($this->dreamWord->getAdapter()->quoteInto('alias=?',$alias));
        $stm = $query->query();
        $word = $stm->fetch();
        if(!$word){
            throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
        }
        return $word;
    }

    public function getDescriptionsByWord($wordId, $typeAlias = null){
        /*
        $data = $this->dreamWordDescription
            ->fetchAll($this->dreamWordDescription->getAdapter()->quoteInto('word_id=?',$wordId));
        if($data){
            $data = $data->toArray();
        }
        */
        $query = $this->dreamWordDescription->select();
        $query->from(array('dvd' => 'dream_word_description'))
            ->setIntegrityCheck(false)
            ->joinLeft(array('dt' => 'dream_type'),'dvd.type_id = dt.id',array('type_description' => 'description','type_name' => 'name'))
            ->where($this->dreamWordDescription->getAdapter()->quoteInto('word_id=?', $wordId));
        $stm = $query->query();
        $data = $stm->fetchAll();

        if(!is_null($typeAlias) && $typeAlias){
            $type = $this->getTypeByAlias($typeAlias);
            $firstElement = array();
            foreach($data as $index => $item){
                if($item['type_id'] == $type['id']){
                    $firstElement = $item;
                    unset($data[$index]);
                    break;
                }
            }
            array_unshift($data, $firstElement);
        }
        return $data;
    }

    public function getAllWords(){
        $words = $this->dreamWord->fetchAll(true);
        if($words){
            $words->toArray();
        }
        return $words;
    }

    public function getWordsByIds($ids){
        $query = $this->dreamWord->select();
        $query->from(array('dream_word'))
            ->setIntegrityCheck(false)
            ->where($this->dreamWord->getAdapter()->quoteInto('id IN(?)', $ids));
        $stm = $query->query();
        return  $stm->fetchAll();
    }
}