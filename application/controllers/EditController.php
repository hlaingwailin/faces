<?php

class EditController extends Zend_Controller_Action
{

    private $numEditRowsAffected;
    private $message;

    public function preDispatch(){
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

    public function postDispatch(){
        if ($this->getRequest()->isPost() && $this->numEditRowsAffected > 0){
            $this->message->success = Model_Constants_Constant::$SUCCESS_SAVE_MESSAGE;
        }elseif($this->getRequest()->isPost() && empty($this->numEditRowsAffeted)){
            $this->message->success = Model_Constants_Constant::$ERROR_SAVE_MESSAGE;
        }
    }

    public function buildingoperatorsAction(){
        $id = $this->_getParam('Buo_Id');
        $tblBuildingOperators = new Model_DbTable_BuildingOperators();
        $buildingOperatorForm = new Form_BuildingOperatorsForm();

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->numEditRowsAffected = $tblBuildingOperators->editModel($formdata);
        }

        $buildingOperatorData = $tblBuildingOperators->find($id)->toArray();
        $buildingOperatorForm->populate($buildingOperatorData[0]);

        $this->view->form = $buildingOperatorForm;
    }

    public function buildingsAction(){
        $id = $this->_getParam('Bul_Id');

        $tblBuildings = new Model_DbTable_Buildings();
        $buildingForm = new Form_BuildingsForm();

        if($this->getRequest()->isPost()){
             $formdata = $this->getRequest()->getPost();
            $this->numEditRowsAffected =$tblBuildings->editModel($formdata);
        }


        $buildingData = $tblBuildings->find($id)->toArray();
        $tblBuildingOperator = new Model_DbTable_BuildingOperators();
        $selectionList = $tblBuildingOperator->getSelectionList(array('value' => 'Buo_Id', 'text' => 'Buo_Name'));
        $buildingForm->Bul_OperatorId->addMultiOptions($selectionList);
        $buildingForm->populate($buildingData[0]);


        $this->view->form = $buildingForm;
    }

    public function shopsAction(){
        $id = $this->_getParam('Sho_Id');

        if(empty($id)){
            $this->_redirect('/lists/shops');
        }

        $tblShops = new Model_DbTable_Shops();
        $shopForm = new Form_ShopsForm();

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->numEditRowsAffected = $tblShops->editModel($formdata);
        }

        $tblBuildings = new Model_DbTable_Buildings();
        $selectionList = $tblBuildings->getSelectionList(array('value' => 'Bul_Id', 'text' => 'Bul_Name'));
        $shopForm->Sho_BuildingId->addMultiOptions($selectionList);

        $shopData = $tblShops->getAdapter()->fetchAll($tblShops->getFindOneWithParentDataSelector($id));
        $shopForm->populate($shopData[0]);

        $combinedData = $this->prepareShopCombineData($id);//print_r($combinedData);

        $this->view->combinedData = $combinedData;
        $this->view->form = $shopForm;
    }

    public function shopoperatorsAction(){
        $id = $this->_getParam('Sop_Id');

        $tblShopOperator = new Model_DbTable_Companies();
        $shopOperatorsForm = new Form_ShopOperatorsForm();

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->numEditRowsAffected =$tblShopOperator->editModel($formdata);
        }

        $shopOperatorData = $tblShopOperator->find($id)->toArray();
        $shopOperatorsForm->populate($shopOperatorData[0]);

        $this->view->form = $shopOperatorsForm;
    }

    public function meterdataAction(){
        $id = $this->_getParam('Met_Id');

        $tblMeterData = new Model_DbTable_MeterData();
        $meterDataForm = new Form_MeterDataForm();

        if($this->getRequest()->isPost()){
            $formdata = $this->getRequest()->getPost();
            $this->numEditRowsAffected = $tblMeterData->editModel($formdata);
        }

        $meterDataSelector = $tblMeterData->getFindOneWithParentDataSelector($id);
        $meterDataArr = $tblMeterData->getAdapter()->fetchRow($meterDataSelector);
        $meterDataForm->populate($meterDataArr);

        $this->view->form = $meterDataForm;
    }

    public function meterratesAction(){
        $id = $this->_getParam('Rat_Id');

        $tblRates = new Model_DbTable_Rates();
        $rateForm = new Form_RatesForm();

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->numEditRowsAffected = $tblRates->editModel($formdata);
        }

        $ratesData = $tblRates->find($id)->toArray();
        $rateForm->populate($ratesData[0]);

        $this->view->form = $rateForm;
    }

    public function billingagentsAction(){
        $id = $this->_getParam('Agn_Id');

        $tblBillingAgents = new Model_DbTable_BillingAgents();
        $billingAgentsForm = new Form_BillingAgentsForm();

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->numEditRowsAffected = $tblBillingAgents->editModel($formdata);
        }

        $billingAgentsData = $tblBillingAgents->find($id)->toArray();
        $billingAgentsForm->populate($billingAgentsData[0]);

        $this->view->form = $billingAgentsForm;
    }

    public function retailersAction()
    {
        $id = $this->_getParam('Ret_Id');

        $tblRetailers = new Model_DbTable_Retailers();
        $retailersForm = new Form_RetailersForm();

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $this->numEditRowsAffected = $tblRetailers->editModel($formdata);
        }

        $retailersData = $tblRetailers->find($id)->toArray();
        $retailersForm->populate($retailersData[0]);

        $this->view->form = $retailersForm;
    }



    /************************** Privare Helper Methods **********************************************************/

    private function populateMultiSelectionElements(array $selectionElements){

    }

    private function prepareShopCombineData($id){
        // shop info
        $tblShops = new Model_DbTable_Shops();
        $shopData = $tblShops->find($id)->toArray();

        // Shop Operator Data
        $tblShopOperators = new Model_DbTable_Companies();
        $shopOperatorData = $tblShopOperators->find($shopData[0]['Sho_OperatorId'])->toArray();

        // Building Info
        $tblBuildings = new Model_DbTable_Buildings();
        $buildingData = $tblBuildings->find($shopData[0]['Sho_BuildingId'])->toArray();

        // Deposits Info
        $tblDeposits = new Model_DbTable_Deposits();
        $depositData = $tblDeposits->findAllByExactCriteria(array('Dep_ShopId' => $shopData[0]['Sho_Id']));

        // Meter Reads
        $tblMeterData = new Model_DbTable_MeterData();
        $meterData = $tblMeterData->findAllByExactCriteria(array('Met_CustomerId' => $shopData[0]['Sho_CustomerId']));

        // Invoices that have been issued so far
        $tblInvoices = new Model_DbTable_Invoices();
        $searchCriteria['Inv_CustomerId'] = $shopData[0]['Sho_CustomerId'];
        $invoicesData = $tblInvoices->findAllWithChildDataBySearchCriteria($searchCriteria);

        // Payments
        // $tblPayments = new Model_DbTable_Invoices();
        // $paymentData = $tblPayments->findAllWithChildDataBySearchCriteria(array('Inv_CustomerId' => $shopData[0]['Sho_CustomerId']));

        $combineData = array(
            'shopData' => $shopData,
            'shopOperatorData' => $shopOperatorData,
            'buildingData' => $buildingData,
            'depositData' => $depositData,
            'meterData' => $meterData,
            'invoicesData' => $invoicesData
        );

        return $combineData;

    }
}

?>