<?php

class App_PollService {

    /**
     * @var App_PollService instance
     */
    private static $instance = null;

    private $poll;

    private $pollOption;

    protected $pollDataCacheName;

    private function __construct()
    {
        $this->poll = new Application_Model_DbTable_PollTable();
        $this->pollOption = new Application_Model_DbTable_PollOptionTable();

        $this->pollDataCacheName = str_replace('.','_', $_SERVER['HTTP_HOST']) . '_poll_data';
    }

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new App_PollService();
        }
        return self::$instance;
    }

    public function buildPollQuery(){
        return $this->poll->getAdapter()->select()->from('poll')->order('id DESC');
    }

    public function addPoll($data){
        $insertData = array(
            'name' => $data['name']
        );
        $this->poll->insert($insertData);
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->pollDataCacheName);
    }

    public function updatePoll($data, $id){
        $updateData = array(
            'name' => $data['name']
        );
        $this->poll->update($updateData,$this->poll->getAdapter()->quoteInto('id=?',$id));
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->pollDataCacheName);
    }

    public function removePoll($id){
        $this->poll->delete($this->poll->getAdapter()->quoteInto('id=?',$id));
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->pollDataCacheName);
    }

    public function getPollById($id){
        $query = $this->poll->getAdapter()->select();
        $query->from(array('poll'))
            ->where($this->poll->getAdapter()->quoteInto('id=?',$id));
        $stm = $query->query();
        return $stm->fetch();
    }

    public function searchPoll($squery){
        $query =$this->poll->getAdapter()->select();
        if(!empty($squery)){
            $query->from(array('poll'))
                ->where('name LIKE \'%'.$squery.'%\' ')->order('id DESC');
        }else{
            $query->from('poll')->order('id DESC');
        }
        $stm = $query->query();
        return $stm->fetchAll();
    }

    public function getOptionsByPollId($id){
        return $this->pollOption->fetchAll($this->pollOption->getAdapter()->quoteInto('poll_id=?',$id))
            ->toArray();
    }

    public function addPollOption($data, $id){
        $insertData = array(
            'name' => $data['name'],
            'poll_id' => $id
        );
        $this->pollOption->insert($insertData);
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->pollDataCacheName);
    }

    public function getPollOptionById($id){
        $query = $this->pollOption->getAdapter()->select();
        $query->from(array('poll_option'))
            ->where($this->pollOption->getAdapter()->quoteInto('id=?', $id));
        $stm = $query->query();
        return $stm->fetch();
    }

    public function updatePollOption($data, $id){
        $updateData = array(
            'name' => $data['name']
        );
        $this->pollOption->update($updateData, $this->pollOption->getAdapter()->quoteInto('id=?',$id));
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->pollDataCacheName);
    }

    public function removePollOption($id){
        $this->pollOption->delete($this->pollOption->getAdapter()->quoteInto('id=?',$id));
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->pollDataCacheName);
    }

    public function changeActivity($id){
        $poll = $this->getPollById($id);
        $updateData = array(
            'activity' => 'n'
        );
        $this->poll->update($updateData, true);
        $cache = Zend_Registry::get('cache');
        $cache->remove($this->pollDataCacheName);
        if($poll['activity'] == 'n'){
            $updateData['activity'] = 'y';
            $this->poll->update($updateData, $this->poll->getAdapter()->quoteInto('id=?',$id));
            return 'true';
        }
        return 'false';
    }

    public function getActivePoll(){
        $cache = Zend_Registry::get('cache');
        if(!$poll = $cache->load($this->pollDataCacheName)){
            $query = $this->poll->getAdapter()->select();
            $query->from(array('p'=>'poll'))
                ->where("p.activity='y'");
            $stm = $query->query();
            $poll = $stm->fetch();

            if($poll) {
                $query = $this->pollOption->getAdapter()->select();
                $query->from(array('poll_option'))
                    ->where($this->pollOption->getAdapter()->quoteInto('poll_id=?', $poll['id']))
                    ->order('id DESC');
                $stm = $query->query();
                $poll['options'] = $stm->fetchAll();
                $cache->save($poll, $this->pollDataCacheName);
            }
        }
        return $poll;
    }

    public function incrementValues($values){
        $error = false;
        $result = array('status' => 'fail', 'options' => array());
        if($values && !empty($values)){
            $values = explode(';', $values);
            foreach($values as $index => $value){
                if(!is_numeric($value)){
                    unset($values[$index]);
                }
            }
            $option = $this->getPollOptionById($values[0]);
            if(isset($_COOKIE['poll_' . md5($option['poll_id'])])){
                return $result;
            }
            $updateData = array(
                'value' => new Zend_Db_Expr('value+1')
            );
            try {
                $this->pollOption->update($updateData, $this->pollOption->getAdapter()->quoteInto('id IN (?)', $values));
                $cache = Zend_Registry::get('cache');
                $cache->remove($this->pollDataCacheName);
                if(!$error && !empty($values)){
                    $result = array('status' => 'success');
                    $query = $this->pollOption->getAdapter()->select();
                    $query->from(array('poll_option'),array('name','value'))
                        ->where($this->pollOption->getAdapter()->quoteInto('poll_id =?', $option['poll_id']))
                        ->order('id DESC');
                    $stm = $query->query();
                    $result['options'] = $stm->fetchAll();
                    setcookie('poll_'.md5($option['poll_id']), 'poll', time() + 3600*24*365, '/');
                }
            }catch(Exception $ex){
                return $result;
            }
        }
        return $result;
    }

}