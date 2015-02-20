<?php

class InvoiceController extends Zend_Controller_Action
{

    private $insertedRowId;
    private $message;
    private $params;

    // action helpers
    private $dateUtil;
    private $invoiceHelper;


    private static $LEFT_MARGIN = 60;
    private static $SECOND_COLUMN_LEFT_MARGIN;
    private static $TOP_MARGIN = 50;
    private static $PAGE_HEIGHT;
    private static $PAGE_WIDTH;
    private static $DEFAULT_LINE_HEIGHT = 15;

    public function preDispatch()
    {
        if (!$this->_helper->security->isAuthenticatedAdmin()) {
            $this->_redirect('/admin/index');
        }

        //var_dump($this->invoiceHelper->rollDays(new DateTime(), 1));exit;
    }

    public function init()
    {
        $this->getHelper('layout')->setLayout('admin');

        // for success or error messages : store in session
        $this->message = new Zend_Session_Namespace("message");
        $this->view->message = $this->message;

        // set all params
        $this->setAllParams();

        $this->dateUtil = $this->_helper->dateUtil;
        $this->invoiceHelper = $this->_helper->invoices;

    }

    public function postDispatch()
    {
        if ($this->getRequest()->isPost() && !empty($this->insertedRowId)) {
            $this->message->success = Model_Constants_Constant::$SUCCESS_SAVE_MESSAGE;
        } elseif ($this->getRequest()->isPost() && empty($this->insertedRowId)) {
            $this->message->success = Model_Constants_Constant::$ERROR_SAVE_MESSAGE;
        }
    }

    public function viewAction(){

    }

    public function batchrunAction(){

        if ($this->getRequest()->isPost()) {

            $formdata = $this->getRequest()->getPost();
            //$tblMeterData = new Model_DbTable_MeterData();
            //$selector = $tblMeterData->getFindAllWithParentDataSelector();
            //$current = $tblMeterData->getMaxInvoiceRunningNumber($selector,$formdata['batchNumber'], $formdata['Bul_Id']);
            //print_r($current);exit;

            $tblMeterData = new Model_DbTable_MeterData();
            $selector = $tblMeterData->getFindAllWithParentDataSelector();
            $selector = $tblMeterData->populateSelectorWithBatchInvoiceRunParameters($selector, $formdata['batchNumber'], $formdata['Bul_Id']);

            $dataArr = $tblMeterData->getAdapter()->fetchAll($selector);

            //$dataArr = $tblMeterData->findAll();
            $pdf = new Zend_Pdf();

            foreach($dataArr as $key => $meterData){
                $invoiceData = $this->prepareInvoiceData($meterData['Met_Id']);

                $issueInvoiceParams = array("overwrite" => $formdata['overwrite']);

                // generate invoice number and pass it to pdf method .. If already generated , return existing invoice number
                $invoiceNumber = $this->invoiceHelper->generateInvoiceNumber($invoiceData);
                $issueInvoiceParams['generatedInvoiceNumber'] = $invoiceNumber;


                $this->generatePdfContent($pdf, $key, $invoiceData);


                // get each invoice from batch and save it
                $individualInvoice = new Zend_Pdf();
                $individualInvoice = clone $pdf;
                unset($individualInvoice->pages);

                $individualInvoice->pages[0] = $pdf->pages[$key];
                //$individualInvoice->pages[0] = $pdf->pages[$key];

                // create folder if doesn't exist
                if(!is_dir($this->invoiceHelper->getInvoiceSavingFolder($meterData['Met_CustomerId']))){
                    mkdir($this->invoiceHelper->getInvoiceSavingFolder($meterData['Met_CustomerId']));
                }

                // invoice is issued first time
                if (empty($invoiceData['issuedInvoicesData'])) {
                    // save pdf file
                    $pdf->save($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $invoiceNumber . ".pdf");
                } else { // invoice is already issued

                    // by right, if invoice is already issued, file should exist .. but check again anyways
                    if (file_exists($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $invoiceData['issuedInvoicesData'][0]['Inv_InvoiceNumber'] . ".pdf")) {
                        // if file exists and overwrite is true, update
                        if ($this->wantToOverwriteExistingInvoice($issueInvoiceParams)) {
                            $pdf->save($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $invoiceNumber . ".pdf", true);
                        } else { // if not overwrite , then generate new file name
                            $newFileName = $this->invoiceHelper->getNewInvoiceFileName($invoiceData['issuedInvoicesData'][0]['Inv_InvoiceNumber']);
                            $pdf->save($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $newFileName . ".pdf");
                        }
                    } else {
                        // crete new pdf
                        $pdf->save($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $invoiceNumber . ".pdf");
                    }
                }

                // insert new row or update in invoices table
                $invoiceRecord = array("Inv_Id" => $invoiceData['issuedInvoicesData'][0]['Inv_Id'], "Inv_InvoiceNumber" => empty($newFileName) ? $invoiceNumber : $newFileName, "Inv_CustomerId" => $invoiceData['shopData'][0]['Sho_CustomerId'], "Inv_TotalAmount" => "", "Inv_DueDate" => $this->invoiceHelper->getPaymentDueDateInDefaultFormat($this->dateUtil->getToday(), $invoiceData['shopData'][0]['Sho_PaymentTerm']));
                $Inv_Id = $this->invoiceHelper->saveIssuedInvoiceRecord($invoiceRecord);
                // update meterdata record with invoice id
                $this->invoiceHelper->updateMeterDataRecordAfterInvoiceIssue($invoiceData['meterData'][0], $Inv_Id);

                // insert new row in invoicerunningnumber table if it is first time creation
                if (empty($invoiceData['issuedInvoicesData'])) {
                    $this->invoiceHelper->updateInvoiceRunningNumber($invoiceData['buildingData'][0]['Bul_InvoicePrefix'], $invoiceData['meterData'][0]['Met_BatchId'], $invoiceNumber);
                }

                if($key == 50){ // to be removed on production
                    break;
                }
            }

            $this->getResponse()->setHeader('Content-type', 'application/pdf');
            echo $this->view->render($pdf->render());
            exit;
        }
    }

    public function createAction(){

        if ($this->getRequest()->isPost()) {

            $formdata = $this->getRequest()->getPost();

            $meterDataId = $formdata['Met_Id'];
            $issueInvoiceParams = array("terminate" => $formdata['terminate'], "overwrite" => $formdata['overwrite'], "terminatedDate" => $formdata['Sho_TerminatedDate'], "chargeStartDate" => $formdata['chargeStartDate']);

            $invoiceData = $this->prepareInvoiceData($meterDataId);

            // generate invoice number and pass it to pdf method .. If already generated , return existing invoice number
            $invoiceNumber = $this->invoiceHelper->generateInvoiceNumber($invoiceData);
            $issueInvoiceParams['generatedInvoiceNumber'] = $invoiceNumber;

            $pdf = new Zend_Pdf();
            $this->generatePdfContent($pdf, 0, $invoiceData, $issueInvoiceParams);

            // create folder if doesn't exist
            if (!is_dir($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']))) {
                mkdir($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']));
            }

            // invoice is issued first time
            if (empty($invoiceData['issuedInvoicesData'])) {
                // save pdf file
                $pdf->save($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $invoiceNumber . ".pdf");
            } else { // invoice is already issued

                // by right, if invoice is already issued, file should exist .. but check again anyways
                if (file_exists($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $invoiceData['issuedInvoicesData'][0]['Inv_InvoiceNumber'] . ".pdf")) {
                    // if file exists and overwrite is true, update
                    if($this->wantToOverwriteExistingInvoice($issueInvoiceParams)){
                        $pdf->save($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $invoiceNumber . ".pdf", true);
                    }else{ // if not overwrite , then generate new file name
                         $newFileName = $this->invoiceHelper->getNewInvoiceFileName($invoiceData['issuedInvoicesData'][0]['Inv_InvoiceNumber']);
                         $pdf->save($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $newFileName . ".pdf");
                    }

                } else {
                    // crete new pdf
                    $pdf->save($this->invoiceHelper->getInvoiceSavingFolder($invoiceData['meterData'][0]['Met_CustomerId']) . $invoiceNumber . ".pdf");
                }
            }

            // insert new row or update in invoices table
            $invoiceRecord = array("Inv_Id" => $invoiceData['issuedInvoicesData'][0]['Inv_Id'], "Inv_InvoiceNumber" => empty($newFileName) ? $invoiceNumber : $newFileName, "Inv_CustomerId" => $invoiceData['shopData'][0]['Sho_CustomerId'], "Inv_TotalAmount" => "", "Inv_DueDate" => $this->invoiceHelper->getPaymentDueDateInDefaultFormat($this->dateUtil->getToday(), $invoiceData['shopData'][0]['Sho_PaymentTerm']));
            $Inv_Id = $this->invoiceHelper->saveIssuedInvoiceRecord($invoiceRecord);
            // update meterdata record with invoice id
            $this->invoiceHelper->updateMeterDataRecordAfterInvoiceIssue($invoiceData['meterData'][0], $Inv_Id);

            // insert new row in invoicerunningnumber table if it is first time creation
            if (empty($invoiceData['issuedInvoicesData'])) {
                 $this->invoiceHelper->updateInvoiceRunningNumber($invoiceData['buildingData'][0]['Bul_InvoicePrefix'], $invoiceData['meterData'][0]['Met_BatchId'], $invoiceNumber);
            }

            $this->getResponse()->setHeader('Content-type', 'application/pdf');
            echo $this->view->render($pdf->render());
            exit;

        }

    }

    public function testAction(){
        $pdf = new Zend_pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);

        $image = Zend_Pdf_Image::imageWithPath('./images/scissors.jpg');
        $page->drawImage($image, 100, 100, 130, 120);

        $this->getResponse()->setHeader('Content-type', 'application/pdf');
        echo $this->view->render($pdf->render());
        exit;
    }

    public function fabonacciAction(){
          $num = $this->_getParam('num');

          if($num < 2){
              echo $num;exit;
          }

          $fb1 = 0; $fb2 = 1;
          for($i = 2; $i <= $num; $i ++){
               $fb = $fb1 + $fb2;
               $fb1 = $fb2;
               $fb2 = $fb;

          }

           echo $fb;exit;
    }



    /************************** Private Helper Methods ***********************************************/

     private function generatePdfContent(Zend_Pdf &$pdf, $pageNo, $invoiceData, $parameters = array()){
         //$pdf = new Zend_pdf();
         $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);

         self::$PAGE_HEIGHT = $page->getHeight();
         self::$PAGE_WIDTH = $page->getWidth();
         self::$SECOND_COLUMN_LEFT_MARGIN = self::$LEFT_MARGIN + 300;

         $style = new Zend_Pdf_Style();
         $style->setLineColor(new Zend_Pdf_Color_RGB(0, 0, 0));
         $style->setFillColor(new Zend_Pdf_Color_GrayScale(0));
         $style->setLineWidth(0.4);

         $this->setNormalText($page,$style);
         $page->drawText("Issued on behalf of \n", self::$LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN));

         $this->setBoldText($page,$style);
         $page->drawText($invoiceData['buildingOperatorData'][0]['Buo_Name'], self::$LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 15));


         // Display Building Operator Info
         $this->setNormalText($page, $style);
         $arrayKeys = array('Buo_Address1' => '', 'Buo_Address2' => '', 'Buo_PostalCode' => 'Singapore', 'Buo_RegistrationNo' => 'Company Reg No:', 'Buo_GSTRegistrationNo' => 'GST Reg No:');
         $startPos = self::$TOP_MARGIN + 40;
         foreach($invoiceData['buildingOperatorData'][0] as $key => $value){

             if(in_array($key, array_keys($arrayKeys))){
                 if(!empty($arrayKeys[$key])){
                     $page->drawText($arrayKeys[$key] . ' ' . $value, self::$LEFT_MARGIN, $this->getHeight($startPos));
                 }else{
                     $page->drawText($value, self::$LEFT_MARGIN, $this->getHeight($startPos));
                 }

                 $startPos = $startPos + self::$DEFAULT_LINE_HEIGHT;
             }
         }


         // Display Retailer Info
         $page->drawText("Authorised Retailer", self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 10));
         $this->setBoldText($page, $style);
         $page->drawText($invoiceData['retailerData'][0]['Ret_Name'], self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 25));
         $this->setNormalText($page, $style);
         $page->drawText("(Reg. No. " . $invoiceData['retailerData'][0]['Ret_RegistrationNo'] . ")", self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 40));
         $arrayKeys = array('Ret_Address1' => '', 'Ret_Address2' => '', 'Ret_PostalCode' => 'Singapore');
         $startPos = self::$TOP_MARGIN + 55;
         foreach ($invoiceData['retailerData'][0] as $key => $value) {

             if (in_array($key, array_keys($arrayKeys))) {
                 if (!empty($arrayKeys[$key])) {
                     $page->drawText($arrayKeys[$key] . ' ' . $value, self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight($startPos));
                 } else {
                     $page->drawText($value, self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight($startPos));
                 }

                 $startPos = $startPos + self::$DEFAULT_LINE_HEIGHT;
             }
         }

         // Display Shop Operator Info
         $this->setBoldText($page, $style);
         $page->drawText("To:", self::$LEFT_MARGIN + 10, $this->getHeight(self::$TOP_MARGIN + 150));
         $page->drawText($invoiceData['shopOperatorData'][0]['Sop_Name'], self::$LEFT_MARGIN + 50, $this->getHeight(self::$TOP_MARGIN + 150));
         $arrayKeys = array('Sop_MailingAddress1' => '', 'Sop_MailingAddress2' => '', 'Sop_PostalCode' => 'Singapore');
         $startPos = self::$TOP_MARGIN + 165;
         $this->setNormalText($page, $style);
         foreach ($invoiceData['shopOperatorData'][0] as $key => $value) {

             if (in_array($key, array_keys($arrayKeys))) {
                 if (!empty($arrayKeys[$key])) {
                     $page->drawText($arrayKeys[$key] . ' ' . $value, self::$LEFT_MARGIN + 50, $this->getHeight($startPos));
                 } else {
                     $page->drawText($value, self::$LEFT_MARGIN + 50, $this->getHeight($startPos));
                 }

                 $startPos = $startPos + self::$DEFAULT_LINE_HEIGHT;
             }
         }
         $page->drawText("Attn:  " . $invoiceData['shopOperatorData'][0]['Sop_Attention'], self::$LEFT_MARGIN + 50, $this->getHeight(self::$TOP_MARGIN + 230));

         // Display Tax Invoice
         $this->setBoldText($page, $style, 15);
         if($this->isFinalInvoice($parameters)){
             $page->drawText("FINAL TAX INVOICE", self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 135));
         }else{
             $page->drawText("TAX INVOICE", self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 135));
         }
         $this->setNormalText($page, $style);
         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 155), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 155));
         $page->drawText("Account No:", self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 170));
         $page->drawText($invoiceData['shopData'][0]['Sho_CustomerId'], self::$SECOND_COLUMN_LEFT_MARGIN + 110, $this->getHeight(self::$TOP_MARGIN + 170));

         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 176), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 176));
         $page->drawText("Invoice No:", self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 191));
         $page->drawText($parameters['generatedInvoiceNumber'], self::$SECOND_COLUMN_LEFT_MARGIN + 110, $this->getHeight(self::$TOP_MARGIN + 191));
         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 197), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 197));
         $page->drawText("Date of Invoice:", self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 213));
         $page->drawText($this->invoiceHelper->getInvoiceDate(), self::$SECOND_COLUMN_LEFT_MARGIN + 110, $this->getHeight(self::$TOP_MARGIN + 213));
         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 219), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 219));
         $page->drawText("Deposit:", self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 234));
         $page->drawText("$" . $invoiceData['depositData'][0]['Dep_Amount'], self::$SECOND_COLUMN_LEFT_MARGIN + 110, $this->getHeight(self::$TOP_MARGIN + 234));
         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 240), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 240));

         // Draw Vertical Lines
         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN - 1, $this->getHeight(self::$TOP_MARGIN + 155), self::$SECOND_COLUMN_LEFT_MARGIN - 1, $this->getHeight(self::$TOP_MARGIN + 240));
         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN + 104, $this->getHeight(self::$TOP_MARGIN + 155), self::$SECOND_COLUMN_LEFT_MARGIN + 104, $this->getHeight(self::$TOP_MARGIN + 240));
         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 155), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 240));

         // Summary of charge
         $this->setNormalText($page, $style);
         $page->drawText("This is your tax invoice in respect of " . $invoiceData['shopData'][0]['Sho_PremiseAddress'] . ", " . $invoiceData['buildingData'][0]['Bul_Name'], self::$LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 265));
         $page->drawLine(self::$LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 270), self::$SECOND_COLUMN_LEFT_MARGIN - 20, $this->getHeight(self::$TOP_MARGIN + 270));
         $this->setBoldText($page, $style);
         if($this->isFinalInvoice($parameters)){
             $page->drawText("SUMMARY OF CHARGES FROM " . $this->invoiceHelper->convertToInvoiceDateFormat($parameters['chargeStartDate']) . " to " . $this->invoiceHelper->convertToInvoiceDateFormat($parameters['terminatedDate']), self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 285));
         }else{
             $page->drawText("SUMMARY OF CHARGES FROM " . $this->dateUtil->getFirstDayOfMonth($invoiceData['meterData'][0]['Met_CurrentUsageEndDate']) . " to " . $this->invoiceHelper->convertToInvoiceDateFormat($invoiceData['meterData'][0]['Met_CurrentUsageEndDate']), self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 285));
         }
         $this->setNormalText($page, $style);
         $page->drawLine(self::$LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 290), self::$SECOND_COLUMN_LEFT_MARGIN - 20, $this->getHeight(self::$TOP_MARGIN + 290));

         // find out previous invoice balance and payment
         $previousInvoiceBalance = $this->invoiceHelper->getPreviousInvoiceData($invoiceData['meterData'][0]['Met_MeterSerialNumber'], $invoiceData['meterData'][0]['Met_CustomerId'], $invoiceData['meterData'][0]['Met_BatchId']);
         if(!empty($previousInvoiceBalance)){
             $previousInvoicePaymentData = $this->invoiceHelper->getPaymentDataForSpecificInvoice($previousInvoiceBalance['Inv_Id']);
             $outstandingBalance = $this->invoiceHelper->getInvoiceOutstandingBalance($previousInvoiceBalance['Inv_TotalAmount'], $previousInvoicePaymentData['Pay_Amount']);
         }

         $page->drawText("Balance B/F from previous Invoice", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 305));
         //$page->drawText($this->invoiceHelper->formatNumber($previousInvoiceBalance['Inv_TotalAmount']), self::$LEFT_MARGIN + 230, $this->getHeight(self::$TOP_MARGIN + 305));
         $this->drawTextRightAligned($page, $this->invoiceHelper->formatNumber($previousInvoiceBalance['Inv_TotalAmount']), 325, $this->getHeight(self::$TOP_MARGIN + 305));
         $page->drawText("Payment received as at " . $this->invoiceHelper->convertToInvoiceDateFormat($previousInvoicePaymentData['Pay_CreatedOn']), self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 320));
         //$page->drawText($this->invoiceHelper->formatNumber($previousInvoicePaymentData['Pay_Amount']), self::$LEFT_MARGIN + 230, $this->getHeight(self::$TOP_MARGIN + 320));
         $this->drawTextRightAligned($page, $this->invoiceHelper->formatNumber($previousInvoicePaymentData['Pay_Amount']), 325, $this->getHeight(self::$TOP_MARGIN + 320));
         $page->drawText("Outstanding Balance", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 335));
         //$page->drawText(empty($outstandingBalance) ? '-' : $outstandingBalance, self::$LEFT_MARGIN + 230, $this->getHeight(self::$TOP_MARGIN + 335));
         $this->drawTextRightAligned($page, empty($outstandingBalance) ? '-' : $this->invoiceHelper->formatNumber($outstandingBalance), 325, $this->getHeight(self::$TOP_MARGIN + 335));
         $page->drawText("Total Current charges due on       " . $this->invoiceHelper->getPaymentDueDate($this->dateUtil->getToday(), $invoiceData['shopData'][0]['Sho_PaymentTerm']), self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 360));
         $page->drawLine(self::$LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 365), self::$SECOND_COLUMN_LEFT_MARGIN - 20, $this->getHeight(self::$TOP_MARGIN + 365));
         $this->setBoldText($page, $style);
         // if has giro account
         if(!empty($invoiceData['shopOperatorData'][0]['Sop_GiroAccountNo'])){
             $page->drawText("GIRO deduction date " . $this->invoiceHelper->getPaymentDueDate($this->dateUtil->getToday(), $invoiceData['shopData'][0]['Sho_PaymentTerm']), self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 378));
         }else{
             $page->drawText("Please pay by " . $this->invoiceHelper->getPaymentDueDate($this->dateUtil->getToday(), $invoiceData['shopData'][0]['Sho_PaymentTerm']), self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 378));
         }
         $page->drawLine(self::$LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 383), self::$SECOND_COLUMN_LEFT_MARGIN - 20, $this->getHeight(self::$TOP_MARGIN + 383));

         // Draw Vertical Lines
         $page->drawLine(self::$LEFT_MARGIN - 1, $this->getHeight(self::$TOP_MARGIN + 270), self::$LEFT_MARGIN - 1, $this->getHeight(self::$TOP_MARGIN + 383));
         $page->drawLine(self::$LEFT_MARGIN + 205, $this->getHeight(self::$TOP_MARGIN + 290), self::$LEFT_MARGIN + 205, $this->getHeight(self::$TOP_MARGIN + 383));
         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN - 20, $this->getHeight(self::$TOP_MARGIN + 270), self::$SECOND_COLUMN_LEFT_MARGIN - 20, $this->getHeight(self::$TOP_MARGIN + 383));

         // Biling Enquires
         //$this->drawBox($page, self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 270), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 350));
         $page->drawRectangle(self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 270), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 350), Zend_Pdf_Page::SHAPE_DRAW_STROKE);
         $this->setNormalText($page, $style);
         $page->drawText("For billing enquiries", self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 280));
         $page->drawText("Telephone                                  " . $invoiceData['billingAgentData'][0]['Agn_Telephone'], self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 295));
         $page->drawText("Facsimile                                    " . $invoiceData['billingAgentData'][0]['Agn_Facsimile'], self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 310));
         $page->drawText("Email              " . $invoiceData['billingAgentData'][0]['Agn_Email'], self::$SECOND_COLUMN_LEFT_MARGIN + 5, $this->getHeight(self::$TOP_MARGIN + 325));

         // Details of charges
         $this->setBoldText($page, $style);
         $this->drawBox($page, self::$LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 400), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 550));
         $page->drawLine(self::$LEFT_MARGIN + 360, $this->getHeight(self::$TOP_MARGIN + 500), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 500));
         $page->drawLine(self::$LEFT_MARGIN + 360, $this->getHeight(self::$TOP_MARGIN + 535), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 535));

         $page->drawText("CURRENT MONTH CHARGES", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 410));
         $page->drawText("Usage (kWh)", self::$LEFT_MARGIN + 230, $this->getHeight(self::$TOP_MARGIN + 410));
         $page->drawText("Rate ($)", self::$LEFT_MARGIN + 320, $this->getHeight(self::$TOP_MARGIN + 410));
         $page->drawText("Amount ($)", self::$LEFT_MARGIN + 370, $this->getHeight(self::$TOP_MARGIN + 410));
         $page->drawText("Total ($)", self::$LEFT_MARGIN + 450, $this->getHeight(self::$TOP_MARGIN + 410));

         // vertical lines
         $page->drawLine(self::$LEFT_MARGIN + 205, $this->getHeight(self::$TOP_MARGIN + 400), self::$LEFT_MARGIN + 205, $this->getHeight(self::$TOP_MARGIN + 550));
         $page->drawLine(self::$LEFT_MARGIN + 310, $this->getHeight(self::$TOP_MARGIN + 400), self::$LEFT_MARGIN + 310, $this->getHeight(self::$TOP_MARGIN + 550));
         $page->drawLine(self::$LEFT_MARGIN + 360, $this->getHeight(self::$TOP_MARGIN + 400), self::$LEFT_MARGIN + 360, $this->getHeight(self::$TOP_MARGIN + 550));
         $page->drawLine(self::$LEFT_MARGIN + 430, $this->getHeight(self::$TOP_MARGIN + 400), self::$LEFT_MARGIN + 430, $this->getHeight(self::$TOP_MARGIN + 550));
         // box content
         $this->setNormalText($page, $style);
         $page->drawText("Interest (non-taxable) - Jun", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 425));
         $page->drawText("Interest (non-taxable) - Jul", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 440));
         $page->drawText("Electricity *", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 455));
         $page->drawText("Reading taken on 31 Jul 14", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 470));
         $page->drawText("Meter ID: 00064804, Reading: 5318.07", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 510));
         $page->drawText("7% GST on $822.84 *", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 535));

         $this->setBoldText($page, $style, 7);
         $page->drawText("* 01 Jul 14 to 30 Sep 14 SP Services Published Tariff Rate is $0.2568 per kWh", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 560));
         $page->drawText("If payment is not received by the due date, interest will be charged at 1% p.m. from the due date up to and inclusive of the date of receipt", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 568));
         $page->drawText("Payments received after 30 Jul 14 will not be reflected on this invoice", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 576));

         // Cut Line
         $page->setLineWidth(1);
         $page->setLineDashingPattern(array(1,1,4,1));
         $page->drawLine(self::$LEFT_MARGIN-30, $this->getHeight(self::$TOP_MARGIN + 585), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 585));

         // Draw Cheque
         $this->setBoldText($page, $style);
         $page->drawText($invoiceData['shopOperatorData'][0]['Sop_Name'], self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 605));

         $this->setNormalText($page, $style);
         $page->setLineDashingPattern(array());
         $page->drawRectangle(self::$LEFT_MARGIN-0.4, $this->getHeight(self::$TOP_MARGIN + 610), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 730), Zend_Pdf_Page::SHAPE_DRAW_STROKE);

         $page->drawText("For Cheque Payment:", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 625));
         $page->drawText("Crossed cheque is to be made payable to :-", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 645));
         $this->setBoldText($page, $style);
         $page->drawText("\"" . $invoiceData['buildingOperatorData'][0]['Buo_Name'] . "\" " . "by      " . "19 Aug 14", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 655));
         $this->setNormalText($page, $style);
         $page->drawText("Please detach and mail this portion with your cheque to:", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 680));
         $page->drawText($invoiceData['billingAgentData'][0]['Agn_Address1'] . ", " . $invoiceData['billingAgentData'][0]['Agn_Address2'] . ", Singapore " . $invoiceData['billingAgentData'][0]['Agn_PostalCode'], self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 690));
         $page->drawText("(No receipt will be issued)", self::$LEFT_MARGIN + 4, $this->getHeight(self::$TOP_MARGIN + 700));
         $page->drawLine(self::$LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 705), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 705));
         $page->drawText("Total Amount Payable", self::$SECOND_COLUMN_LEFT_MARGIN, $this->getHeight(self::$TOP_MARGIN + 720));

         $this->drawBox($page, self::$SECOND_COLUMN_LEFT_MARGIN + 35, $this->getHeight(self::$TOP_MARGIN + 610), self::$SECOND_COLUMN_LEFT_MARGIN + 200, $this->getHeight(self::$TOP_MARGIN + 680), 4);
         $page->drawLine(self::$SECOND_COLUMN_LEFT_MARGIN + 110, $this->getHeight(self::$TOP_MARGIN + 610), self::$SECOND_COLUMN_LEFT_MARGIN + 110, $this->getHeight(self::$TOP_MARGIN + 680));

         $page->drawText("Account No", self::$SECOND_COLUMN_LEFT_MARGIN + 43, $this->getHeight(self::$TOP_MARGIN + 625));
         $page->drawText($invoiceData['shopData'][0]['Sho_CustomerId'], self::$SECOND_COLUMN_LEFT_MARGIN + 120, $this->getHeight(self::$TOP_MARGIN + 625));
         $page->drawText("Invoice No", self::$SECOND_COLUMN_LEFT_MARGIN + 43, $this->getHeight(self::$TOP_MARGIN + 643));
         $page->drawText("Cheque No", self::$SECOND_COLUMN_LEFT_MARGIN + 43, $this->getHeight(self::$TOP_MARGIN + 659));
         $page->drawText("Bank/Branch", self::$SECOND_COLUMN_LEFT_MARGIN + 43, $this->getHeight(self::$TOP_MARGIN + 676));


         $pdf->pages[$pageNo] = $page;

         return $pdf;
     }

    /**
     * Give you a grid table with equal column widths and row heights
     *
     * @param     $page
     * @param     $topLeftX
     * @param     $topLeftY
     * @param     $rightBottomX
     * @param     $rightBottomY
     * @param int $numRows
     * @param int $numCols
     */

    private function drawBox(&$page, $topLeftX, $topLeftY, $rightBottomX, $rightBottomY, $numRows = 1, $numCols = 1){
         $page->drawLine($topLeftX, $topLeftY, $rightBottomX, $topLeftY);
         $page->drawLine($topLeftX, $rightBottomY, $rightBottomX, $rightBottomY);
         $page->drawLine($topLeftX-0.5, $topLeftY, $topLeftX-0.5, $rightBottomY);
         $page->drawLine($rightBottomX, $topLeftY, $rightBottomX, $rightBottomY);

         if($numCols > 1){
             $colWidth = ($rightBottomX-$topLeftX)/ $numCols;
             for($i=1;$i<$numCols;$i++){
                 $page->drawLine($topLeftX+($i*$colWidth), $topLeftY, $topLeftX+($i * $colWidth), $rightBottomY);
             }
         }

         if($numRows > 1){
             $rowHeight = ($topLeftY-$rightBottomY)/$numRows;
             for($i=1;$i<$numRows;$i++){
                 $page->drawLine($topLeftX, $topLeftY-($i*$rowHeight), $rightBottomX, $topLeftY-($i * $rowHeight));
             }
         }
     }

     private function getHeight($heightFromTop){
         return self::$PAGE_HEIGHT - $heightFromTop;
     }

     private function setBoldText(&$page, &$style, $fontSize = 9){
         $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), $fontSize);
         $page->setStyle($style);
     }

     private function setNormalText(&$page, &$style, $fontSize = 9){
         $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $fontSize);
         $page->setStyle($style);
     }

     private function generateInvoiceFileName($invoiceData){
         $buildingName = $invoiceData['buildingData']['Bul_Name'];
     }

     private function getInvoiceTotalAmount($invoiceData){

     }

     private function getInvoiceDueDate($invoiceData){

     }

     private function isFinalInvoice($parameters){
         if($parameters['terminate'] == 1){
             return true;
         }
         return false;
     }

     private function wantToOverwriteExistingInvoice($parameters){
         if ($parameters['overwrite'] == 1) {
             return true;
         }
         return false;
     }

     private function setAllParams(){
         $this->params = $this->getRequest()->getParams();
     }

     private function prepareInvoiceData($meterDataId){
         // Meter Data
         $tblMeterData = new Model_DbTable_MeterData();
         $meterData = $tblMeterData->find($meterDataId)->toArray();

         // Shop Data
         $tblShops = new Model_DbTable_Shops();
         $shopData = $tblShops->findAllBySearchCriteria(array('Sho_CustomerId' => $meterData[0]['Met_CustomerId']));

         // Shop Operator Data
         $tblShopOperators = new Model_DbTable_Companies();
         $shopOperatorData = $tblShopOperators->find($shopData[0]['Sho_OperatorId'])->toArray();

         // Building Data
         $tblBuildings = new Model_DbTable_Buildings();
         $buildingData = $tblBuildings->find($shopData[0]['Sho_BuildingId'])->toArray();

         // Building Operator Data
         $tblBuildingOperators = new Model_DbTable_BuildingOperators();
         $buildingOperatorData = $tblBuildingOperators->find($buildingData[0]['Bul_OperatorId'])->toArray();

         // Retailer Data
         $tblRetailers = new Model_DbTable_Retailers();
         $retailerData = $tblRetailers->findAll(); // for now there is only one retailer

         // Billing Agent Data
         $tblBillingAgents = new Model_DbTable_BillingAgents();
         $billingAgentData = $tblBillingAgents->findAll();

         // Deposit Data
         $tblDeposits = new Model_DbTable_Deposits();
         $depositData = $tblDeposits->findAllBySearchCriteria(array('Dep_ShopId' => $shopData[0]['Sho_Id']));

         // Invoices Data
         $tblInvoices = new Model_DbTable_Invoices();
         $issuedInvoicesData = $tblInvoices->find($meterData[0]['Met_InvoiceId'])->toArray();

         $invoiceData = array(
             'meterData' => $meterData,
             'shopData' => $shopData,
             'shopOperatorData' => $shopOperatorData,
             'buildingData' => $buildingData,
             'buildingOperatorData' => $buildingOperatorData,
             'retailerData' => $retailerData,
             'billingAgentData' => $billingAgentData,
             'depositData' => $depositData,
             'issuedInvoicesData' => $issuedInvoicesData
         );

         return $invoiceData;

     }

    private function drawTextRightAligned(&$page, $content, $endX, $yPos){
         $characterWidth = Model_Constants_Constant::$DEFAULT_CHARACTER_WIDTH;

         $numCharacters = strlen($content);
         $contentLength = $characterWidth * $numCharacters;
         $startX = $endX - $contentLength;
         $page->drawText($content, $startX, $yPos);

    }


}

?>