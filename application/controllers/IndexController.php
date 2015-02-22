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


        $test[] = array('Usage' => $usage = 100, 'Rate' => $rate = $this->getRate(), 'Amount' => $usage * $rate);

        print_r($test);exit;
    }

    private function getRate(){
        return 0.443;
    }
}

?>