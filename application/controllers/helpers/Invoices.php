<?php
class My_Action_Helper_Invoices extends Zend_Controller_Action_Helper_Abstract
{

    private $dateUtil;

    const DEFAULT_INVOICE_DATE_FORMAT = 'd M y';
    const DEFAULT_DATE_FORMAT = "Y-m-d H:i:s";

    function __construct(){
         $this->dateUtil = new My_Action_Helper_DateUtil();
    }

    function generateInvoiceNumber($invoiceData){

      //  if(!empty($invoiceData['meterData'][0]['Met_InvoiceNumber'])){
      //      return $invoiceData['meterData'][0]['Met_InvoiceNumber'];
      //  }

        $tblRunningNumber = new Model_DbTable_InvoiceRunningNumber();
        if(empty($invoiceData['meterData'][0]['Met_InvoiceId'])){ // first time invoice issue
             $row = $tblRunningNumber->getMaxInvoiceRunningNumber($invoiceData['buildingData'][0]['Bul_InvoicePrefix'], $invoiceData['meterData'][0]['Met_BatchId']);

             if(!empty($row['currentMaxRunningNumber'])){
                 $nextRunningNumber = $row['currentMaxRunningNumber'] + 1;
             }else{
                 $nextRunningNumber = 1;
             }

             $paddedRunningNumber = $this->getPaddedNumber($nextRunningNumber);

             return $this->constructInvoiceNumber($invoiceData['buildingData'][0]['Bul_InvoicePrefix'], $invoiceData['meterData'][0]['Met_BatchId'], $paddedRunningNumber);

        }else{ // not first time invoice issue
             return $invoiceData['issuedInvoicesData'][0]['Inv_InvoiceNumber'];
        }

    }

    /**
     * E.g. TAM1407001.pdf .. This function will return TAM1407001_01.pdf
     * E.g. TAM1407001_01.pdf .. This function will return TAM1407001_02.pdf
     *
     * @param $currentFileName
     */
    function getNewInvoiceFileName($currentFileName){
         $partsArr = explode("_", $currentFileName);

         if(!empty($partsArr[1])){ // there is xxxxxxxx_1.pdf
             // 1.pdf for example
             $fileNumber = $partsArr[1];
             $newFileNumber = $fileNumber + 1;

             return $partsArr[0] . "_" . $newFileNumber;
         }else{ // just reutrn xxxxxxxxx_01.pdf
             return $currentFileName . "_1";
         }
    }

    function getInvoiceDate(){
        return $this->dateUtil->convertDateFormat('now', self::DEFAULT_INVOICE_DATE_FORMAT);
    }

    function getPaymentDueDate($date,$paymentTerm){
        // Today + Payment Term

        $dateObj = new DateTime($date);
        return $this->convertInvoiceDateFormat($this->dateUtil->rollDays($dateObj, $paymentTerm));
    }

    function getPaymentDueDateInDefaultFormat($date, $paymentTerm){
        $dateObj = new DateTime($date);
        $dueDate = $this->dateUtil->rollDays($dateObj, $paymentTerm);
        return $dueDate->format(self::DEFAULT_DATE_FORMAT);
    }

    function convertInvoiceDateFormat(DateTime $dateObj){
        return $dateObj->format(self::DEFAULT_INVOICE_DATE_FORMAT);
    }

    /**
     *  Accept date string as a parameter and convert it into date format used in invoice (03 NOV 14)
     *
     * @param $date string
     *
     * @return string
     */
    function convertToInvoiceDateFormat($date){
        $dateObj = new DateTime($date);
        return $dateObj->format(self::DEFAULT_INVOICE_DATE_FORMAT);
    }

    /**
     * Invoice files are saved under /public/invoices/{customerAccountNo}/
     *
     * @param $customerAccountNo
     *
     * @return string
     */
    function getInvoiceSavingFolder($customerAccountNo){
        $folder = Model_Constants_Constant::$DEFAULT_INVOICE_FOLDER . "/" . $customerAccountNo . "/";
        return $folder;
    }

    function isAlreadyIssuedInvoice($meterdataId){
        $table = new Model_DbTable_MeterData();
        $row = $table->find($meterdataId)->toArray();

        if(empty($row[0]['Met_InvoiceId'])){
            return false;
        }else{
            return true;
        }

    }

    function findRateForGivenReadDate($readDate){

    }

    function findStartDateForLastInvoice($meterDataRecord){
         if(!empty($meterDataRecord['Met_'])){

         }
    }

    /**
     *
     * E.g. TAM1407001 .. Retrieve '001' and increment 1
     *
     * @param $buildingPrefix
     * @param $batchId
     * @param $invoiceNumber
     */
    function updateInvoiceRunningNumber($buildingPrefix, $batchId, $invoiceNumber){
          $partsArr = explode($batchId, $invoiceNumber);
          $number = ltrim($partsArr[1], "0");

          $tblRunningNumber = new Model_DbTable_InvoiceRunningNumber();
          $numRowsUpdated = $tblRunningNumber->incrementRunningNumber($buildingPrefix, $batchId, $number);

    }

    function saveIssuedInvoiceRecord($invoiceRecord){
        $table = new Model_DbTable_Invoices();

        return $table->saveModel($invoiceRecord);
    }

    function updateMeterDataRecordAfterInvoiceIssue($meterdata, $invoiceId){
        $table = new Model_DbTable_MeterData();
        $meterdata['Met_InvoiceId'] = $invoiceId;
        $table->editModel($meterdata);
    }

    function getIssuedInvoiceLink($meterdataId){
        $table = new Model_DbTable_MeterData();
        $row = $table->find($meterdataId)->toArray();

        $tblInv = new Model_DbTable_Invoices();
        $invData = $tblInv->find($row[0]['Met_InvoiceId']);

        return $this->getInvoiceSavingFolder($row[0]['Met_CustomerId']) . $invData[0]['Inv_InvoiceNumber'] . ".pdf";
    }

    // get the bacthId (Eg. 1407) from meter data , then retrieve meter data record with bacth 1406 , then retrieve corresponding invoice
    function getPreviousInvoiceData($meterSerialNumber, $customerId, $currentBatchId){
        $previousBatchId = $this->getPreviousBatchId($currentBatchId);

        // retrieve meter data with batch Id, meter no, customer id
        $table = new Model_DbTable_MeterData();
        $searchCriteria = array('Met_MeterSerialNumber' => $meterSerialNumber, 'Met_CustomerId' => $customerId, 'Met_BatchId' => $previousBatchId);
        $previousMeterDataArr = $table->findAllBySearchCriteria($searchCriteria);

        // retrieve Invoice Id from previous meter data record
        $previousInvoiceId = $previousMeterDataArr[0]['Met_InvoiceId'];

        // retrieve from invoice table using invoice id
        $tblInvoice = new Model_DbTable_Invoices();
        $invoiceArr = $tblInvoice->find($previousInvoiceId)->toArray();

        $previousInvoiceData= $invoiceArr[0];

        return $previousInvoiceData;
    }

    function getPaymentDataForSpecificInvoice($Inv_Id){
        // get invoice number from invoice id
        $tblInvoice = new Model_DbTable_Invoices();
        $invoiceData = $tblInvoice->find($Inv_Id)->getRow(0);
        $invoiceNumber = $invoiceData['Inv_InvoiceNumber'];

        // get payment from invoice number
        $tblPayment = new Model_DbTable_Payments();
        $paymentData = $tblPayment->findOneBySearchCriteria(array('Pay_InvoiceNumber' => $invoiceNumber));

        return $paymentData;
    }

    function getInvoiceOutstandingBalance($previousBalance, $paidAmount){
         $outstanding = $previousBalance - $paidAmount;

         if($outstanding == 0){
            return "-";
         }

         return $outstanding;
    }

    function formatNumber($number){
        return number_format($number, 2, '.', '');
    }

    /************************** Privare Helper Methods ***********************************************/


    private function getPaddedNumber($number){
        return str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    private function constructInvoiceNumber($prefix, $batchNumber, $paddedNumber){
        return $prefix . $batchNumber . $paddedNumber;
    }

    private function getPreviousBatchId($currentBatchId){
        // if 1500 , then should return 1412
        $year = substr($currentBatchId, 0, 2);
        $month = substr($currentBatchId, 2, 2);
        if($month == '01'){
            $prevYear = $year - 1;
            $previousBatchId = $prevYear . '12';
        }else{
            $previousBatchId = $currentBatchId - 1;
        }

        return $previousBatchId;
    }
}

?>