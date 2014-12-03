<?php
class Admin_CategoryController extends Zend_Controller_Action{
	
	protected $service;
	
	public function preDispatch(){
		$this->service = new App_CategoryService();
	}
	
	public function indexAction(){
		//$this->view->categories = $this->service->getCategories();
		$this->view->types = $this->service->getCategoryTypes();
		//var_dump($this->view->types); die;
	}
	
	public function getCategoriesJsonAction(){
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        //$start = $this->service->time();
        $categories = $this->service->structuredCategories();
        //var_dump($this->service->time()  - $start); die;
        echo Zend_Json::encode($categories);
	}
	
	public function saveCategoriesJsonAction(){
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $categories = $this->_getParam('json');
        $this->service->saveCategories(Zend_Json::decode($categories,Zend_Json::TYPE_ARRAY));
        //var_dump($categories);
        //die;
	}
	
	public function addAction(){
		$form = new Application_Form_CategoryForm();
		if($this->getRequest()->isPost()){
			$formData = $this->_getAllParams();
			if($form->isValid($formData)){
				$this->service->addCategory($form->getValidValues($formData));
				$this->redirect('/admin/category');
			}else{
				$this->view->form = $form;
				$form->populate($formData);
			}
		}else{
			$this->view->form = $form;
		}
		$this->render('edit');
	}
	
	public function editAction(){
		
	}
	public function removeAction(){
		$this->service->removeCategory($this->_getParam('id'));
		$this->_helper->viewRenderer->setNoRender();
		$this->redirect('/admin/category');
	}
	
	public function imageAction(){
		$categories = $this->service->structuredCategories();
		$this->view->categories = $categories;
	}
	
	public function showCategoryImageAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$catId = $this->_getParam('category',false);
		echo Zend_Json::encode($this->service->getCategory($catId));
	}
	
	public function saveImageAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		if ($this->getRequest()->isPost() && $_FILES["image"]["error"] > 0){
			echo "Error: " . $_FILES["image"]["error"] . "<br>";
		}else{
			
			$catId = $this->_getParam('category',false);
			//var_dump($catId); die;
			if($catId && !empty($_FILES["image"]["name"])){
		
				$path = realpath(dirname('.')).DIRECTORY_SEPARATOR.
				'files'.DIRECTORY_SEPARATOR.'divinations'.DIRECTORY_SEPARATOR;
				$ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
				$newName = uniqid().'.'.$ext;
				$filepath = $path.$newName;
				move_uploaded_file($_FILES["image"]["tmp_name"],	$filepath);
				$category = $this->service->getCategory($catId);
				if(!empty($category['image']) && file_exists($path.$category['image'])){
					unlink($path.$category['image']);
				}
				$this->service->updateCategoryImage($catId,$newName);
				echo Zend_Json::encode(array('image' => $newName));
			}
		}
	}
}