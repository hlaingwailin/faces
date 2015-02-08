<?php

class AddController extends Zend_Controller_Action
{

    private $insertedRowId;
    private $message;

    public function preDispatch()
    {
        if (!$this->_helper->security->isAuthenticatedAdmin()) {
            $this->_redirect('/admin/index');
        }
    }

    public function init()
    {
        $this->getHelper('layout')->setLayout('admin');

        // for success or error messages : store in session
        $this->message = new Zend_Session_Namespace("message");
        $this->view->message = $this->message;
    }

    public function postDispatch()
    {
        if ($this->getRequest()->isPost() && !empty($this->insertedRowId)) {
            $this->message->success = Model_Constants_Constant::$SUCCESS_SAVE_MESSAGE;
        } elseif ($this->getRequest()->isPost() && empty($this->insertedRowId)) {
            $this->message->success = Model_Constants_Constant::$ERROR_SAVE_MESSAGE;
        }
    }

    public function buildingoperatorsAction()
    {
        $tblBuildingOperators = new Model_DbTable_BuildingOperators();
        $buildingOperatorForm = new Form_BuildingOperatorsForm();
        $actionUrl = $this->_helper->Url->url(array('controller' => $this->getRequest()->getControllerName(), 'action' => $this->getRequest()->getActionName()), null, true);
        $buildingOperatorForm->setAction($actionUrl);

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->insertedRowId = $tblBuildingOperators->saveModel($formdata);

            $buildingOperatorData = $tblBuildingOperators->find($this->insertedRowId)->toArray();
            $buildingOperatorForm->populate($buildingOperatorData[0]);
        }

        $this->view->form = $buildingOperatorForm;
    }

    public function buildingsAction()
    {
        $tblBuildings = new Model_DbTable_Buildings();
        $buildingForm = new Form_BuildingsForm();
        $actionUrl = $this->_helper->Url->url(array('controller' => $this->getRequest()->getControllerName(), 'action' => $this->getRequest()->getActionName()), null, true);
        $buildingForm->setAction($actionUrl);

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->insertedRowId = $tblBuildings->saveModel($formdata);

            $buildingData = $tblBuildings->find($this->insertedRowId)->toArray();
            $buildingForm->populate($buildingData[0]);
        }

        $tblBuildingOperator = new Model_DbTable_BuildingOperators();
        $selectionList = $tblBuildingOperator->getSelectionList(array('value' => 'Buo_Id', 'text' => 'Buo_Name'));
        $buildingForm->Bul_OperatorId->addMultiOptions($selectionList);

        $this->view->form = $buildingForm;
    }

    public function shopoperatorsAction()
    {
        $tblShopOperator = new Model_DbTable_Companies();
        $shopOperatorsForm = new Form_ShopOperatorsForm();
        $actionUrl = $this->_helper->Url->url(array('controller' => $this->getRequest()->getControllerName(), 'action' => $this->getRequest()->getActionName()), null, true);
        $shopOperatorsForm->setAction($actionUrl);

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->insertedRowId = $tblShopOperator->saveModel($formdata);

            $shopOperatorData = $tblShopOperator->find($this->insertedRowId)->toArray();
            $shopOperatorsForm->populate($shopOperatorData[0]);
        }

        $this->view->form = $shopOperatorsForm;
    }

    public function meterratesAction(){
        $tblRates = new Model_DbTable_Rates();
        $ratesForm = new Form_RatesForm();
        $actionUrl = $this->_helper->Url->url(array('controller' => $this->getRequest()->getControllerName(), 'action' => $this->getRequest()->getActionName()), null, true);
        $ratesForm->setAction($actionUrl);

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->insertedRowId = $tblRates->saveModel($formdata);

            $ratesData = $tblRates->find($this->insertedRowId)->toArray();
            $ratesForm->populate($ratesData[0]);
        }

        $this->view->form = $ratesForm;
    }

    public function billingagentsAction(){
        $tblBillingAgents = new Model_DbTable_BillingAgents();
        $billingAgentsForm = new Form_BillingAgentsForm();
        $actionUrl = $this->_helper->Url->url(array('controller' => $this->getRequest()->getControllerName(), 'action' => $this->getRequest()->getActionName()), null, true);
        $billingAgentsForm->setAction($actionUrl);

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->insertedRowId = $tblBillingAgents->saveModel($formdata);

            $billingAgentsData = $tblBillingAgents->find($this->insertedRowId)->toArray();
            $billingAgentsForm->populate($billingAgentsData[0]);
        }

        $this->view->form = $billingAgentsForm;
    }

    public function retailersAction()
    {
        $tblRetailers = new Model_DbTable_Retailers();
        $retailersForm = new Form_RetailersForm();
        $actionUrl = $this->_helper->Url->url(array('controller' => $this->getRequest()->getControllerName(), 'action' => $this->getRequest()->getActionName()), null, true);
        $retailersForm->setAction($actionUrl);

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->insertedRowId = $tblRetailers->saveModel($formdata);

            $retailersData = $tblRetailers->find($this->insertedRowId)->toArray();
            $retailersForm->populate($retailersData[0]);
        }

        $this->view->form = $retailersForm;
    }

}

?>