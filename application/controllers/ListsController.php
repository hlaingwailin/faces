<?php
class ListsController extends Zend_Controller_Action
{
    public function init()
    {
        $this->getHelper('layout')->setLayout('admin');

        if (!$this->_helper->security->isAuthenticatedAdmin()) {
            $this->_redirect('/admin/index');
        }

        // for success or error messages : store in session
        $this->message = new Zend_Session_Namespace("message");
        $this->view->message = $this->message;
    }

    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        echo "test";
    }

    public function listAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        echo "list";
    }

    public function buildingsAction(){
        $tblBuildings = new Model_DbTable_Buildings();
        $selector = $tblBuildings->getFindAllWithParentDataSelector($this->getSortInfo());

        $headerColMap = array("Name" => "Bul_Name", "Operator" => "Buo_Name", "Address 1" => "Bul_Address1", "Address 2" => "Bul_Address2", "Postal Code" => "Bul_PostalCode", "Prefix" => "Bul_InvoicePrefix");

        if ($this->isSearch()) {
            $selector = $tblBuildings->populateSelectorWithSearchCriteria($selector, $this->getSearchCriteriaValues());
        }

        $this->passDisplayDataToView($selector, $headerColMap);
    }

    public function buildingoperatorsAction(){
        $tblBuildingOperators = new Model_DbTable_BuildingOperators();
        $selector = $tblBuildingOperators->getFindAllSelector($this->getSortInfo());

        $headerColMap = array("Name" => "Buo_Name", "Address 1" => "Buo_Address1", "Address 2" => "Buo_Address2", "Postal Code" => "Buo_PostalCode", "Com. Reg. No" => "Buo_RegistrationNo", "GST Reg. No" => "Buo_GSTRegistrationNo");

        if ($this->isSearch()) {
            $selector = $tblBuildingOperators->populateSelectorWithSearchCriteria($selector, $this->getSearchCriteriaValues());
        }

        $this->passDisplayDataToView($selector, $headerColMap);
    }

    public function shopsAction(){
        $tblShops = new Model_DbTable_Shops();
        $selector = $tblShops->getFindAllWithParentDataSelector($this->getSortInfo());

        $headerColMap = array("Customer Id" => "Sho_CustomerId", "Name" => "Sho_Name", "Building" => "Bul_Name", "Premise Address" => "Sho_PremiseAddress", "Meter Id" => "Sho_MeterId");

        if ($this->isSearch()) {
            $selector = $tblShops->populateSelectorWithSearchCriteria($selector, $this->getSearchCriteriaValues());
        }

        $this->passDisplayDataToView($selector, $headerColMap);
    }

    public function shopoperatorsAction(){
        $tblShopOperators = new Model_DbTable_Companies();
        $selector = $tblShopOperators->getFindAllSelector($this->getSortInfo());

        $sortColArr = array("Sop_Name");
        $headerColMap = array("Company Name" => "Sop_Name", "Address 1" => "Sop_MailingAddress1", "Address 2" => "Sop_MailingAddress2", "Postal Code" => "Sop_PostalCode", "Giro Account No." => "Sop_GiroAccountNo", "Attention" => "Sop_Attention");

        if($this->isSearch()){
            $selector = $tblShopOperators->populateSelectorWithSearchCriteria($selector, $this->getSearchCriteriaValues());
        }

        $this->passDisplayDataToView($selector, $headerColMap, $sortColArr);
    }

    public function meterdataAction(){
        $tblMeterData = new Model_DbTable_MeterData();
        $selector = $tblMeterData->getFindAllWithParentDataSelector($this->getSortInfo());

        $headerColMap = array("Meter Serial No" => "Met_MeterSerialNumber", "Customer Id" => "Met_CustomerId", "Shop Name" => "Sho_Name", "Last Read Usage" => "Met_LastReadUsage", "Last Read Date" => "Met_LastReadDate", "Current Read Usage" => "Met_CurrentReadUsage", "Current Read Date" => "Met_CurrentReadDate");

        if ($this->isSearch()) {
            $selector = $tblMeterData->populateSelectorWithSearchCriteria($selector, $this->getSearchCriteriaValues());
        }

        $batchInvoiceRunForm = new Form_BatchInvoiceRunForm();
        $tblBuilding = new Model_DbTable_Buildings();
        $selectionList = $tblBuilding->getSelectionList(array('value' => 'Bul_Id', 'text' => 'Bul_Name'));
        $batchInvoiceRunForm->Bul_Id->addMultiOptions($selectionList);

        $this->view->form = $batchInvoiceRunForm;
        $this->passDisplayDataToView($selector, $headerColMap);
    }

    public function meterratesAction(){
        $tblRates = new Model_DbTable_Rates();
        $selector = $tblRates->getFindAllSelector($this->getSortInfo());

        $headerColMap = array("Start Date" => "Rat_StartDate", "End Date" => "Rat_EndDate", "Amount" => "Rat_Value");

        if ($this->isSearch()) {
            $selector = $tblRates->populateSelectorWithSearchCriteria($selector, $this->getSearchCriteriaValues());
        }

        $this->passDisplayDataToView($selector, $headerColMap);
    }

    public function billingagentsAction(){
        $tblBillingAgents = new Model_DbTable_BillingAgents();
        $selector = $tblBillingAgents->getFindAllSelector($this->getSortInfo());

        $headerColMap = array("Name" => "Agn_Name", "Address1" => "Agn_Address1", "Address2" => "Agn_Address2", "Telephone" => "Agn_Telephone", "Facsimile" => "Agn_Facsimile", "Email" => "Agn_Email");

        if ($this->isSearch()) {
            $selector = $tblBillingAgents->populateSelectorWithSearchCriteria($selector, $this->getSearchCriteriaValues());
        }

        $this->passDisplayDataToView($selector, $headerColMap);
    }

    public function retailersAction()
    {
        $tblRetailers = new Model_DbTable_Retailers();
        $selector = $tblRetailers->getFindAllSelector($this->getSortInfo());

        $headerColMap = array("Name" => "Ret_Name", "Address1" => "Ret_Address1", "Address2" => "Ret_Address2", "Telephone" => "Ret_Telephone", "Facsimile" => "Ret_Facsimile", "Email" => "Ret_Email", "Registration No" => "Ret_RegistrationNo");

        if ($this->isSearch()) {
            $selector = $tblRetailers->populateSelectorWithSearchCriteria($selector, $this->getSearchCriteriaValues());
        }

        $this->passDisplayDataToView($selector, $headerColMap);
    }

    public function interestsAction(){
        $tblInterest = new Model_DbTable_Interests();
        $selector = $tblInterest->getFindAllWithParentDataSelector($this->getSortInfo());

        // Get Payment Date to search
        $paymentCreateDate = $this->_getParam('Pay_CreatedOn');

        $headerColMap = array("Customer Id" => "Int_CustomerId", "Invoice No." => "Int_InvoiceNumber", "Overdue Days" => "Int_NumberOfOverdueDays", "Interest" => "Int_InterestAmount", "Due Date" => "Inv_DueDate", "Pay Date" => "Pay_PaymentDate", "Created" => "Pay_CreatedOn");

        $searchCriteria = array();
        if(!empty($paymentCreateDate)){
            $searchCriteria = array('Pay_CreatedOn' => $paymentCreateDate);
        }

        if ($this->isSearch()) {
            $searchCriteria = $searchCriteria + $this->getSearchCriteriaValues();
            $selector = $tblInterest->populateSelectorWithSearchCriteria($selector, $searchCriteria, true);
        }

        $this->passDisplayDataToView($selector, $headerColMap);
    }

    private function getPaginator($selector, $itemPerPage){
        $adapter = new Zend_Paginator_Adapter_DbSelect($selector);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($itemPerPage)->setCurrentPageNumber($this->_getParam("page", 1));

        return $paginator;
    }

    private function passDisplayDataToView($selector, $headerColMap, $sortColArr = array()){
        $paginator = $this->getPaginator($selector, 50);

        $this->view->headerColMap = $headerColMap;
        $this->view->data = $paginator->getIterator();
        $this->view->paginator = $paginator;
        $this->view->sortColArr = $sortColArr;
    }

    private function getSortInfo(){
        $sort = array();
        $params = $this->_getAllParams();
        if(!empty($params[Model_Constants_Constant::$SORT_URL_PARAM_NAME])){
            $sort['name'] = $params[Model_Constants_Constant::$SORT_URL_PARAM_NAME];
            $sort['order'] = $params[Model_Constants_Constant::$SORTORDER_URL_PARAM_NAME];
        }
        return $sort;
    }

    private function isSearch(){
        $params = $this->_getAllParams();
        if(array_key_exists(Model_Constants_Constant::$SEARCH_URL_PARAM_NAME,$params)){
            return true;
        }
        return false;
    }

    private function getSearchCriteriaValues()
    {
        $params = $this->_getAllParams();
        return $params[Model_Constants_Constant::$SEARCH_CRITERIA_PARAM_NAME];
    }
}

?>