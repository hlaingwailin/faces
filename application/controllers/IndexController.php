<?php
class IndexController extends Zend_Controller_Action
{
    private $dateUtil;
    private $invoiceHelper;

    public function init(){
        $this->dateUtil = $this->_helper->dateUtil;
        $this->invoiceHelper = $this->_helper->invoices;

    }

    public function indexAction()
    {
        $this->getHelper('layout')->setLayout('index');

    }

    public function testAction(){

    }
}

?>