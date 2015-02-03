<?php

class Admin_DreamController extends Zend_Controller_Action{

    protected $service;

    public function preDispatch(){
        $this->service = App_DreamService::getInstance();
    }

    public function wordListAction(){
        $page = $this->_getParam('page','');

        $query = $this->service->listWordQuery();

        $this->view->page = $page;

        $paginator = Zend_Paginator::factory($query);
        $paginator->setCurrentPageNumber($page,'');
        $paginator->setItemCountPerPage(50);
        $paginator->setPageRange(7);
        $this->view->paginator = $paginator;
    }

    public function addWordAction(){
        $form = new Application_Form_WordForm();
        $this->view->form = $form;
        $this->view->actionType = 'add';

        if($this->getRequest()->isPost()){
            $formData = $this->_getAllParams();
            if($form->isValid($formData)){
                $this->service->addWord($form->getValidValues($formData));
                $this->redirect('/admin/dream/word-list');
            }else{
                $form->populate($formData);
            }
        }
        $this->render('edit-word');
    }
    public function editWordAction(){
        $form = new Application_Form_WordForm();
        $this->view->form = $form;
        $this->view->actionType = 'edit';

        $id = $this->_getParam('id',false);
        $word = $this->service->getWordById($id);

        $form->word->setValue($word['word']);
        $form->title->setValue($word['title']);
        $form->keywords->setValue($word['keywords']);
        $form->seodescription->setValue($word['description']);

        $page = $this->_getParam('page','');
        $form->getElement('page')->setValue($page);

        if($this->getRequest()->isPost()){
            $formData = $this->_getAllParams();
            if($form->isValid($formData)){
                $this->service->updateWord($form->getValidValues($formData),$word['id']);
                $this->redirect((!empty($page))?'/admin/dream/word-list/page/'.$page :'/admin/dream/word-list');
            }else{
                $form->populate($formData);
            }
        }
        $this->render('edit-word');
    }

    public function searchWordAction(){
        $this->_helper->layout->disableLayout();
        $query = $this->_getParam('query','');
        $data = $this->service->searchWord($query);
        $this->view->data = $data;
        $this->render('search-word');
    }

    public function removeWordAction(){
        $id = $this->_getParam('id',false);
        if(null !== $id){
            $this->service->removeWord($id);
            $page = $this->_getParam('page','');
            $this->redirect((!empty($page))?'/admin/dream/word-list/page/'.$page :'/admin/dream/word-list');
        }
    }

    public function typeListAction(){
        $page = $this->_getParam('page','');

        $query = $this->service->listTypeQuery();

        $this->view->page = $page;

        $paginator = Zend_Paginator::factory($query);
        $paginator->setCurrentPageNumber($page,'');
        $paginator->setItemCountPerPage(50);
        $paginator->setPageRange(7);
        $this->view->paginator = $paginator;
    }

    public function addTypeAction(){
        $form = new Application_Form_DreamTypeForm();
        $this->view->form = $form;
        $this->view->actionType = 'add';

        if($this->getRequest()->isPost()){
            $formData = $this->_getAllParams();
            if($form->isValid($formData)){
                $this->service->addType($form->getValidValues($formData));
                $this->redirect('/admin/dream/type-list');
            }else{
                $form->populate($formData);
            }
        }
        $this->render('edit-type');
    }

    public function editTypeAction(){
        $form = new Application_Form_DreamTypeForm();
        $this->view->form = $form;
        $this->view->actionType = 'edit';

        $id = $this->_getParam('id',false);
        $type = $this->service->getTypeById($id);

        $form->name->setValue($type['name']);
        $form->description->setValue($type['description']);
        $form->title->setValue($type['title']);
        $form->keywords->setValue($type['keywords']);
        $form->seodescription->setValue($type['seodescription']);


        $page = $this->_getParam('page','');
        $form->getElement('page')->setValue($page);

        if($this->getRequest()->isPost()){
            $formData = $this->_getAllParams();
            if($form->isValid($formData)){
                $this->service->updateType($form->getValidValues($formData), $type['id']);
                $this->redirect((!empty($page))?'/admin/dream/type-list/page/'.$page :'/admin/dream/type-list');
            }else{
                $form->populate($formData);
            }
        }
        $this->render('edit-type');
    }

    public function removeTypeAction(){
        $id = $this->_getParam('id',false);
        if(null !== $id){
            $this->service->removeType($id);
            $page = $this->_getParam('page','');
            $this->redirect((!empty($page))?'/admin/dream/type-list/page/'.$page :'/admin/dream/type-list');
        }
    }

    public function wordDescriptionAction(){
        $this->view->words = $this->service->getWords();
        $this->view->types = $this->service->getTypes();
    }

    public function loadWordDescriptionAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $word = $this->_getParam('word', false);
        $type = $this->_getParam('type', false);
        if($word && $type) {
            echo Zend_Json::encode($this->service->getDescriptionByWordAndType($word, $type));
        }
    }

    public function saveWordDescriptionAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $word = $this->_getParam('word', false);
        $type = $this->_getParam('type', false);
        $description = $this->_getParam('description', false);
        if($word && $type) {
            echo Zend_Json::encode($this->service->saveDescriptionByWordAndType($word, $type, $description));
        }
    }

    public function wordsAutocompleteAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $term = $this->_getParam('term',false);
        if($term){
            echo Zend_Json::encode($this->service->getWordAutocomplete($term));
        }
    }

}