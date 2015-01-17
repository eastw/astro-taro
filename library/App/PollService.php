<?php

class App_PollService {

    /**
     * @var App_PollService instance
     */
    private static $instance = null;

    private $poll;

    private $pollOption;

    private function __construct()
    {
        $this->poll = new Application_Model_DbTable_PollTable();
        $this->pollOption = new Application_Model_DbTable_PollOptionTable();
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
    }

    public function updatePoll($data, $id){
        $updateData = array(
            'name' => $data['name']
        );
        $this->poll->update($updateData,$this->poll->getAdapter()->quoteInto('id=?',$id));
    }

    public function removePoll($id){
        $this->poll->delete($this->poll->getAdapter()->quoteInto('id=?',$id));
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
    }

    public function removePollOption($id){
        $this->pollOption->delete($this->pollOption->getAdapter()->quoteInto('id=?',$id));
    }

    public function changeActivity($id){
        $poll = $this->getPollById($id);
        $updateData = array(
            'activity' => 'n'
        );
        $this->poll->update($updateData, true);
        if($poll['activity'] == 'n'){
            $updateData['activity'] = 'y';
            $this->poll->update($updateData, $this->poll->getAdapter()->quoteInto('id=?',$id));
            return 'true';
        }
        return 'false';
    }

}