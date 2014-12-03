<?php
class E2B_Controller_DefaultBaseController extends Zend_Controller_Action
{
 	public function init()
    {
        parent::init();
        //TODO: auto 
    }
    
    public function preDispatch(){
    	parent::preDispatch();
    }
    
    //protected function disableRendering()
}
