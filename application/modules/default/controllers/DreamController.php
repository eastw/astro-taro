<?php
class DreamController extends App_Controller_Action_ParentController{

    protected $service;

    public function init(){
        $this->service = App_DreamService::getInstance();
    }

    public function indexAction(){
        $letter = mb_strtoupper($this->_getParam('letter',false), 'UTF-8');
        if(!in_array($letter, App_DreamService::getAlphabet())){
            throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
        }
        $this->view->pageTitle = 'Сонник';
        $this->view->letter = $letter;
        $this->view->types = $this->service->getAllTypes();
        $this->view->words = $this->service->getWordsByLetter($letter);

        $navItem = $this->view->navigation()->findOneById('dream');//->setActive('true');
        if($navItem){
            $navItem->setActive('true');
        }
    }

    /*
    public function letterAction(){
        $letter = mb_strtoupper($this->_getParam('letter',false), 'UTF-8');
        if(!in_array($letter, App_DreamService::getAlphabet())){
            throw new Zend_Controller_Action_Exception('Что то пошло не так.. Страница не найдена!', 404);
        }
        $this->view->letter = $letter;
        $this->view->types = $this->service->getAllTypes();
        $this->view->words = $this->service->getWordsByLetter($letter);
        $this->view->pageTitle = 'Сонник';
    }
    */

    public function typeAction(){
        $this->view->letter = $this->_getParam('letter','а');
        $this->view->words = $this->service->getWordsByLetter($this->view->letter);
        $this->view->type = $this->service->getTypeByAlias($this->_getParam('type',false));
        $this->view->pageTitle = $this->view->type['name'];

        $this->view->seotitle = $this->view->type['title'];
        $this->view->seokeywords = $this->view->type['keywords'];
        $this->view->seodescription = $this->view->type['seodescription'];

        $navItem = $this->view->navigation()->findOneById($this->view->type['id'] . '-' . $this->view->type['alias']);
        if($navItem){
            $navItem->setActive('true');
        }
    }

    public function wordAction(){
        $this->view->word = $this->service->getWordByAlias($this->_getParam('word',false));
        $this->view->wordDescription = $this->service
                    ->getDescriptionsByWord($this->view->word['id'],
                        $this->_getParam('type',false));
        $this->view->pageTitle = $this->view->word['word'];
        $this->view->topMenuActiveItem = 'dream';
        $this->view->seotitle = $this->view->word['title'];
        $this->view->seokeywords = $this->view->word['keywords'];
        $this->view->seodescription = $this->view->word['description'];

        $navItem = $this->view->navigation()->findOneById($this->view->word['id'] . '-' . $this->view->word['alias']);
        if($navItem){
            $navItem->setActive('true');
        }
        $this->view->attributes = array(
            'type' => 'dream_word',
            'subtype' => '',
            'sign' => '',
            'resource_id' => $this->view->word['id']
        );
        $commentsService = App_CommentsService::getInstance();
        $this->view->comments = $commentsService->getComments('dream_word', '', '', $this->view->word['id']);
    }

    public function searchAction(){
        $query = $this->_getParam('squery',false);
        $page = $this->_getParam('page',false);
        $query = preg_replace('/<\/?[^>]+>/ims','',$query);
        $page = preg_replace('/<\/?[^>]+>/ims','',$page);
        $this->view->pageTitle = 'Вы искали: '.$query;
        $this->view->searchQuery = $query;

        $navItem = $this->view->navigation()->findOneById('dream-search');
        if($navItem){
            $navItem->setActive('true');
        }

        $this->preparePage($page);
        $searchService = new App_SearchService();
        $this->view->data = $searchService->searchDreamWordsOnly($query,$page);
        $this->view->pagination = $searchService->getPagesArray();
    }

    protected function preparePage($page){
        if(is_numeric($page)){
            if($page < 0){
                $this->view->curPage = 1;
            }else{
                $this->view->curPage = $page;
            }
        }else{
            $this->view->curPage = 1;
        }
        //$this->view->curPage = $page;
    }

    public function voteAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam('id',false);

        if($id && $this->getRequest()->isPost()){
            if(!isset($_COOKIE['vote_word_' . $id])){
                setcookie('vote_word_'.$id, 'vote', time() + 3600*24*140, '/');
                $this->service->setVote($id);
            }
        }
    }
}