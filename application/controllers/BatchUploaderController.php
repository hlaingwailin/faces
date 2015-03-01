<?php
class BatchUploaderController extends Zend_Controller_Action
{
    private $message;
    // action helpers
    private $dateUtil;
    private $invoiceHelper;

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

        $this->dateUtil = $this->_helper->dateUtil;
        $this->invoiceHelper = $this->_helper->invoices;

    }

    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

    }

    public function uploadpaymentsAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $form = new Form_UploadForm();
        $form->setLegend("Upload Payment Collections");
        $request = $this->getRequest();

        if ($request->isPost()) {
            $csvArr = $this->convertCsvToArray();
            $dataArr = $this->convertCsvArrayToDataArray($csvArr, Model_Constants_DataMap::$PAYMENTS_MAP);
            if ($this->checkColumnValidity($csvArr[0], Model_Constants_DataMap::$PAYMENTS_MAP)) {
                $table = new Model_DbTable_Payments();
                $numRecords = count($dataArr);
                $numRowsInserted = 0;
                foreach($dataArr as $paymentRecord){
                    $lastInsertId = $table->saveModel($paymentRecord);
                    if(!empty($lastInsertId)){
                        $numRowsInserted++;
                    }

                    $this->handleInterestIfAny($paymentRecord);
                }

                if ($numRowsInserted == $numRecords) {
                    $this->message->success = "All records are successfully saved.";
                }
            } else {
                $this->message->error = "Invalid columns specified in CSV file";
            }
        }

        $this->view->form = $form;

        echo $form;
    }

    public function uploaddepositsAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $form = new Form_UploadForm();
        $form->setLegend("Upload Deposits Data");
        $request = $this->getRequest();

        if ($request->isPost()) {
            $csvArr = $this->convertCsvToArray();
            $dataArr = $this->convertCsvArrayToDataArray($csvArr, Model_Constants_DataMap::$DEPOSIT_MAP);
            if ($this->checkColumnValidity($csvArr[0], Model_Constants_DataMap::$DEPOSIT_MAP)) {
                $table = new Model_DbTable_Deposits();
                $numRecords = count($dataArr);
                $numRowsInserted = $this->saveUploadedData($table, $dataArr);
                if ($numRowsInserted == $numRecords) {
                    $this->message->success = "All records are successfully saved.";
                }
            } else {
                $this->message->error = "Invalid columns specified in CSV file";
            }
        }

        $this->view->form = $form;

        echo $form;
    }


    public function uploadbuildingoperatorsAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $form = new Form_UploadForm();
        $form->setLegend("Upload Building Operators Data");
        $request = $this->getRequest();

        if ($request->isPost()) {
            $csvArr = $this->convertCsvToArray();
            $dataArr = $this->convertCsvArrayToDataArray($csvArr, Model_Constants_DataMap::$BUILDINGS_OPERATOR_MAP);
            if ($this->checkColumnValidity($csvArr[0], Model_Constants_DataMap::$BUILDINGS_OPERATOR_MAP)) {
                $table = new Model_DbTable_BuildingOperators();
                $numRecords = count($dataArr);
                $numRowsInserted = $this->saveUploadedData($table, $dataArr);
                if ($numRowsInserted == $numRecords) {
                    $this->message->success = "All records are successfully saved.";
                }
            } else {
                $this->message->error = "Invalid columns specified in CSV file";
            }
        }

        $this->view->form = $form;

        echo $form;
    }

    public function uploadbuildingsAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $form = new Form_UploadForm();
        $form->setLegend("Upload Buildings Data");
        $request = $this->getRequest();

        if ($request->isPost()) {
            $csvArr = $this->convertCsvToArray();
            $dataArr = $this->convertCsvArrayToDataArray($csvArr, Model_Constants_DataMap::$BUILDINGS_MAP);
            if ($this->checkColumnValidity($csvArr[0], Model_Constants_DataMap::$BUILDINGS_MAP)) {
                $table = new Model_DbTable_Buildings();
                $numRecords = count($dataArr);
                $numRowsInserted = $this->saveUploadedData($table, $dataArr);
                if ($numRowsInserted == $numRecords) {
                    $this->message->success = "All records are successfully saved.";
                }
            } else {
                $this->message->error = "Invalid columns specified in CSV file";
            }
        }

        $this->view->form = $form;

        echo $form;
    }

    public function uploadtenantsAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $form = new Form_UploadForm();
        $form->setLegend("Upload Shops Data");
        $request = $this->getRequest();

        if ($request->isPost()) {
            $csvArr = $this->convertCsvToArray();
            $dataArr = $this->convertCsvArrayToDataArray($csvArr, Model_Constants_DataMap::$TENANTS_MAP);
            if ($this->checkColumnValidity($csvArr[0], Model_Constants_DataMap::$TENANTS_MAP)) {
                $table = new Model_DbTable_Shops();
                $numRecords = count($dataArr);
                $numRowsInserted = $this->saveUploadedData($table, $dataArr);
                if ($numRowsInserted == $numRecords) {
                    $this->message->success = "All records are successfully saved.";
                }
            } else {
                $this->message->error = "Invalid columns specified in CSV file";
            }
        }

        $this->view->form = $form;

        echo $form;
    }

    public function uploadcompaniesAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $form = new Form_UploadForm();
        $form->setLegend("Upload Shop Operators Data");
        $request = $this->getRequest();

        if($request->isPost()){
             $csvArr = $this->convertCsvToArray();
             $dataArr = $this->convertCsvArrayToDataArray($csvArr, Model_Constants_DataMap::$COMPANIES_MAP);
             if($this->checkColumnValidity($csvArr[0], Model_Constants_DataMap::$COMPANIES_MAP)){
                  $table = new Model_DbTable_Companies();
                  $numRecords = count($dataArr);
                  $numRowsInserted = $this->saveUploadedData($table, $dataArr);
                  if($numRowsInserted == $numRecords){
                      $this->message->success = "All records are successfully saved.";
                  }
             }else{
                 $this->message->error = "Invalid columns specified in CSV file";
             }
        }

        $this->view->form = $form;

        echo $form;
    }


    public function uploadmeterdataAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $form = new Form_UploadForm();
        $form->setLegend("Upload Meter Data");
        $request = $this->getRequest();

        if ($request->isPost()) {
            $csvArr = $this->convertCsvToArray();
            $dataArr = $this->convertCsvArrayToDataArray($csvArr, Model_Constants_DataMap::$METER_MAP);
            if ($this->checkColumnValidity($csvArr[0], Model_Constants_DataMap::$METER_MAP)) {
                $table = new Model_DbTable_MeterData();
                $numRecords = count($dataArr);
                $numRowsInserted = $this->saveUploadedData($table, $dataArr);
                if ($numRowsInserted == $numRecords) {
                    $this->message->success = "All records are successfully saved.";
                }
            } else {
                $this->message->error = "Invalid columns specified in CSV file";
            }
        }

        $this->view->form = $form;

        echo $form;
    }

    /*******************************  Private helper functions   ***********************************/

    private function convertCsvToArray(){
        $upload = new Zend_File_Transfer_Adapter_Http();
        $files = $upload->getFileInfo();

        foreach ($files as $fileID => $fileInfo) {
            $file = fopen($fileInfo['tmp_name'], "r");

            while (!feof($file)) {
                $lineArr = fgetcsv($file);
                if($lineArr != null){
                    $csvArr[] = $lineArr;
                }
            }

            fclose($file);
        }

        return $csvArr;
    }

    private function convertCsvArrayToDataArray($csvArr, $dataMap){
         $dataArr[] = array();
         $columnArr = $csvArr[0];

         array_shift($csvArr);

         foreach($csvArr as $k => $v){
             foreach($v as $index => $value){
                 if($this->isLookupNeeded($columnArr[trim($index)])){
                     $lookupMap = $this->getLookupMap($columnArr[trim($index)]);
                     $dataArr[$k][$dataMap[$columnArr[trim($index)]]] = $lookupMap[trim($value)];
                 }else{
                     $dataArr[$k][$dataMap[$columnArr[trim($index)]]] = trim($value);
                 }
             }
         }

        return $dataArr;
    }

    private function saveUploadedData(Model_DbTable_Base $table, array $uploadedData){
        return $table->saveModels($uploadedData);
    }

    private function checkColumnValidity(array $columns, array $columnsMap){

          foreach($columns as $column){
               if(!array_key_exists($column, $columnsMap)){
                   return false;
               }
          }

        return true;
    }

    private function isLookupNeeded($colName){
        $colNameArr = explode("-", $colName);
        return count($colNameArr) > 1 ? true : false;
    }

    private function extractColPrefix($colName){
        $colNameArr = explode("-", $colName);
        $lookUpColArr = explode("_", $colNameArr[1]);
        $colPrefix = $lookUpColArr[0];

        return $colPrefix;
    }

    private function extractLookupColArr($colName){
        $colNameArr = explode("-", $colName);
        $lookUpColArr = explode("_", $colNameArr[1]);
        $colPrefix = $lookUpColArr[0];
        $indexCol = $colPrefix . "_" . $lookUpColArr[1];
        $valueCol = $colPrefix . "_" . $lookUpColArr[2];

        return array("Id" => $indexCol, "Name" => $valueCol);
    }

    private function populateLookupMap($table, $lookupArr){
        return $table->getIdNameLookupMap($lookupArr);
    }

    private function getLookupMap($colName){
         $colPrefix = $this->extractColPrefix($colName);
         $lookupArr = $this->extractLookupColArr($colName);

         $tblName =  Model_Constants_DataMap::$PREFIX_TABLE_MAP[$colPrefix];
         $table = new $tblName();

         $lookupMap = $this->populateLookupMap($table,$lookupArr);

         return $lookupMap;
    }

    private function handleInterestIfAny($paymentRecord){
         // search invoice table with given Invoice number
         $tblInvoice = new Model_DbTable_Invoices();
         $invoiceRecord = $tblInvoice->findOneBySearchCriteria(array('Inv_InvoiceNumber' => $paymentRecord['Pay_InvoiceNumber']));

         // determine if interest should be applied ..
         if($this->invoiceHelper->isInterestApplicable($invoiceRecord['Inv_DueDate'], $paymentRecord['Pay_CreatedOn'])){
             // calculate interest amount
             $outstandingAmount = $invoiceRecord['Inv_TotalAmount'];
             $interestAmount = $this->invoiceHelper->calculateInterest($invoiceRecord['Inv_DueDate'], $paymentRecord['Pay_CreatedOn'], $outstandingAmount);

             // prepare data to insert into interest table
             $interestData = array(
                 'Int_CustomerId' => $invoiceRecord['Inv_CustomerId'],
                 'Int_InvoiceNumber' => $invoiceRecord['Inv_InvoiceNumber'],
                 'Int_NumberOfOverdueDays' => $this->dateUtil->getDaysDifference($invoiceRecord['Inv_DueDate'], $paymentRecord['Pay_CreatedOn']),
                 'Int_InterestAmount' => $interestAmount
             );

             // save interest data
             $tblInterest = new Model_DbTable_Interests();
             $tblInterest->saveModel($interestData);
         }

    }
}

?>