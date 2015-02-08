<?php
class AdminController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getHelper('layout')->setLayout('admin');

        //$helpers = $this->_helper->getExistingHelpers();print_r($helpers);
    }
}

?>