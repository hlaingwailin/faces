<?php

class ViewController extends Zend_Controller_Action
{

    private $id = null;

    public function init()
    {
        if (!$this->_helper->security->isAuthenticatedAdmin()) {
            $this->_redirect('/admin/index');
        }
        $this->getHelper('layout')->setLayout('admin');
        $this->prepareParams();
    }

    public function buildingoperatorsAction()
    {
    }

    public function buildingsAction()
    {
    }

    public function shopAction(){

    }


    /****************** Private Helper Methods ************************************************/

    private function prepareParams(){
        $this->id = $this->_getParam('id');
    }
}

?>