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

        $date1 = "2015-01-10";
        $date2 = "2016-01-14";
        $outstandingAmount = 100;


        echo $this->invoiceHelper->calculateInterest($date1, $date2, $outstandingAmount);
        exit;
    }

    public function arrayAction(){
        $array1 = array('foo' => 'foo');
        $array2 = array('doo' => 'doo');

        print_r($array1 + $array2);exit;
    }

    public function urlAction(){
       // echo $this->getHelper('url')->url(array('controller' => 'lists', 'action' => 'interests', 'Pay_PaymentDate' => "2014"));
        echo $this->getRequest()->getBaseUrl();
        exit;
    }

}

?>