<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16.01.15
 * Time: 12:48
 */
class Admin_PollController extends Zend_Controller_Action {

    protected $service;

    public function preDispatch(){
        $this->service = App_PollService::getInstance();
    }

    public function indexAction(){
        $page = $this->_getParam('page','');

        $query = $this->service->buildPollQuery();

        $this->view->page = $page;

        $paginator = Zend_Paginator::factory($query);
        $paginator->setCurrentPageNumber($page,'');
        $paginator->setItemCountPerPage(50);
        $paginator->setPageRange(7);
        $this->view->paginator = $paginator;
    }

    public function addAction(){
        $form = new Application_Form_PollForm();
        $this->view->form = $form;
        $this->view->actionType = 'add';

        if($this->getRequest()->isPost()){
            $formData = $this->_getAllParams();
            if($form->isValid($formData)){
                $this->service->addPoll($form->getValidValues($formData));
                $this->redirect('/admin/poll');
            }else{
                $form->populate($formData);
            }
        }
        $this->render('edit');
    }

    public function editAction(){
        $form = new Application_Form_PollForm();
        $this->view->form = $form;
        $this->view->actionType = 'edit';

        $id = $this->_getParam('id',false);
        $poll = $this->service->getPollById($id);

        $form->name->setValue($poll['name']);

        $page = $this->_getParam('page','');
        $form->getElement('page')->setValue($page);

        if($this->getRequest()->isPost()){
            $formData = $this->_getAllParams();
            if($form->isValid($formData)){
                $this->service->updatePoll($form->getValidValues($formData),$poll['id']);
                $this->redirect((!empty($page))?'/admin/poll/index/page/'.$page :'/admin/poll');
            }else{
                $form->populate($formData);
            }
        }
        $this->render('edit');
    }

    public function removeAction(){
        $id = $this->_getParam('id',false);
        if(null !== $id){
            $this->service->removePoll($id);
            $page = $this->_getParam('page','');
            $this->redirect((!empty($page))?'/admin/poll/index/page/'.$page :'/admin/poll');
        }
    }

    public function searchAction(){
        $this->_helper->layout->disableLayout();
        $query = $this->_getParam('query','');
        $data = $this->service->searchPoll($query);
        $this->view->data = $data;
        $this->render('search');
    }

    public function optionListAction(){
        $id = $this->_getParam('id',false);
        if($id) {
            $this->view->poll = $this->service->getPollById($id);
            $this->view->data = $this->service->getOptionsByPollId($id);
        }
    }

    public function addOptionAction(){
        $id = $this->_getParam('id',false);
        if($id) {
            $form = new Application_Form_PollOptionForm();
            $this->view->form = $form;
            $this->view->actionType = 'add';
            if ($this->getRequest()->isPost()) {
                $formData = $this->_getAllParams();
                if ($form->isValid($formData)) {
                    $this->service->addPollOption($form->getValidValues($formData), $id);
                    $this->redirect('/admin/poll/option-list/id/' . $id );
                } else {
                    $form->populate($formData);
                }
            }else{
                $this->view->poll = $this->service->getPollById($id);
            }
            $this->render('option-edit');
        }
    }

    public function editOptionAction(){
        $optionId = $this->_getParam('option_id',false);
        $pollId = $this->_getParam('id',false);
        if($optionId) {
            $form = new Application_Form_PollOptionForm();
            $this->view->form = $form;
            $this->view->actionType = 'edit';
            $pollOption = $this->service->getPollOptionById($optionId);
            $form->name->setValue($pollOption['name']);

            if ($this->getRequest()->isPost()) {
                $formData = $this->_getAllParams();
                if ($form->isValid($formData)) {
                    $this->service->updatePollOption($form->getValidValues($formData), $optionId);
                    $this->redirect('/admin/poll/option-list/id/' . $pollId );
                } else {
                    $form->populate($formData);
                }
            }else{
                $this->view->poll = $this->service->getPollById($pollId);
            }
            $this->render('option-edit');
        }
    }

    public function removeOptionAction(){
        $id = $this->_getParam('id',false);
        if($id){
            $this->service->removePollOption($id);
            $this->redirect('/admin/poll/option-list/id/' . $id );
        }
    }

    public function resultAction(){
        $id = $this->_getParam('id', false);
        if($id){
            $this->view->poll = $this->service->getPollById($id);
            $this->view->data = $this->service->getOptionsByPollId($id);
        }
    }

    public function activityAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam('id', false);
        if($id){
            $json = array(
                'activity' => $this->service->changeActivity($id)
            );
            echo Zend_Json::encode($json);
        }
    }
}