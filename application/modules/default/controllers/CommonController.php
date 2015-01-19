<?php
//TODO: rework all front ajax requests for this controller
class CommonController extends Zend_Controller_Action {

    protected $pollService;

    public function init(){

    }

    public function pollResultAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $service = App_PollService::getInstance();
        $result = $service->incrementValues($this->_getParam('values',false));
        echo Zend_Json::encode($result);
    }


}