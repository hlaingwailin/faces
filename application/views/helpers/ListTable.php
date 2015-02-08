<?php

class Zend_View_Helper_ListTable
{

     private $data = array();
     private $baseUrl;
     private $view;
     private $columnPrefix;
     private $sortColArr = array();
     private $currentSortColName =  null;
     private static $actionColName = 'Actions';
     private static $deleteButton = 'b_drop.png';
     private static $editButton = 'b_edit.png';
     private static $viewButton = 'b_view.png';
     private static $invoiceButton = 'b_invoice.png';
     private static $viewInvoiceButton = 'b_pdf.png';

     // service helper
     private $invoiceHelper;
     private $dateHelper;


     public function listTable($view){
         // initialize variables
         $this->view = $view;
         $this->setSetSortColArr($view->sortColArr);
         $this->baseUrl = $view->baseUrl();

         // load helper services : dateUtil helper is used in invoices helper , so need to load it first
         $this->dateHelper = $view->getHelper('dateutil');
         $this->invoiceHelper = $view->getHelper('invoices');

         return $this;
     }

     public function setSetSortColArr(array $sortColArr){
         $this->sortColArr = $sortColArr;
         return $this;
     }

     public function setData(array $data){
         $this->data = $data;
     }

     public function getListTable(array $headerColMap, $data, array $actions = null, $pager = false, $addButton = false){
          //$headerCols = array_keys($headerColMap);
          $selectedCols = array_values($headerColMap);

          $this->columnPrefix = $this->extractColumnPrefix($selectedCols[0]);

          if(empty($this->sortColArr)){
               $this->setDefaultSortColArr($headerColMap);
          }

          $table = $this->constructTableBody($data, $this->constructTableHeader($headerColMap, $this->getTableFrame(), $actions), $selectedCols, $actions);

          if ($addButton == true) {
              $table = $this->constructAddButton($table);
          }

          if($pager == true){
               $table = $this->constructPager($table);
          }

          return $table;
     }

     public function toString(){
          echo 'List Table View Helper';
     }

     /************************************** Private Helper Functions *************************************/

     private function constructAddButton($table){
           $addButton = '
                <a href="' . $this->view->url(array('controller' => 'add', 'action' => $this->view->request()->getActionName()), null, true) . '">
                <button type="button">' . Model_Constants_Constant::$ADD_BUTTON_LABEL . '</button><a>
           ';

           $table = '<div style="float:right;padding-bottom:20px;">' . $addButton . '</div>' . $table;
           return $table;
     }

     private function constructPager($table){
          $pager = $this->view->paginationControl($this->view->paginatior, "Sliding", "pagination.phtml");

          $table = $pager . '<br /><br /><br /><br />' . $table . '<br /><br />' . $pager;

          return $table;
     }

     private function getTableFrame(){
         $tblFrame =
         '<table id="listTable">
            <thead>
            </thead>
            <tbody>
            </tbody>
         </table>';

         return $tblFrame;
     }

     private function constructTableHeader(array $headerCols, $table, array $actions = null){
         $row = '<tr>';
         foreach($headerCols as $col => $dbCol){
              $row .= '<th>' . $this->constructSortColLink($col, $dbCol) . '</th>';
         }

         if(!empty($actions)){
              $row .= '<th>' . self::$actionColName . '</th>';
         }
         $row .= '</tr>';

         return $this->installPart($table, 'thead', $row);
     }

     private function constructTableBody($data, $table, array $selectedCols, array $actions = null){
         $tblRow = '';
         foreach ($data as $row) {
             $tblRow .= '<tr ondblclick="goToLink(\'' . $this->baseUrl . '/edit/' . Model_Constants_DataMap::$PREFIX_TO_ENTITY_MAP[$this->columnPrefix] . '/' . $this->columnPrefix . '_Id/' . $row[$this->columnPrefix . '_Id'] . '\')">';
                  foreach($selectedCols as $colKey){
                      $tblRow .= '<td>' . $row[$colKey] . '</td>';
                  }

                  if(!empty($actions)){
                      $tblRow .= $this->getActionColumn($actions, $this->columnPrefix . '_Id/', $row[$this->columnPrefix . '_Id']);
                  }

             $tblRow .= '</tr>';
         }


         return $this->installPart($table, 'tbody', $tblRow);
     }

     private function installPart($structure, $placeholder, $part){
           $openTag = '<' . $placeholder . '>';
           $closeTag = '</' . $placeholder . '>';;
           $openTagPos = strpos($structure, $openTag);
           $closeTagPos = strpos($structure, $closeTag);
           $firstPart = substr($structure,0, $openTagPos + strlen($openTag));
           $secondPart = substr($structure, $closeTagPos, strlen($structure));

           return $firstPart . $part . $secondPart;
     }

     private function getActionColumn(array $actions, $rowIdentifierKey, $rowIdentifierValue){
          $col = '<td style="width:60px;">';
          foreach($actions as $name => $link){
                switch($name){
                    case "view" :
                        $col .= '&nbsp;<a href="' . $link . '/' . $rowIdentifierKey . $rowIdentifierValue . '"><img title="' . strtoupper($name) .  '" src="' . $this->baseUrl . '/images/' . self::$viewButton . '" /></a>';
                        break;
                    case "edit" :
                        $col .= '&nbsp;<a href="' . $link . '/' . $rowIdentifierKey . $rowIdentifierValue . '"><img title="' . strtoupper($name) . '"  src="' . $this->baseUrl . '/images/' . self::$editButton . '" /></a>';
                        break;
                    case "delete" :
                        $col .= '&nbsp;<a href="' . $link . '/' . $rowIdentifierKey . $rowIdentifierValue . '"><img title="' . strtoupper($name) . '"  src="' . $this->baseUrl . '/images/' . self::$deleteButton . '" /></a>';
                        break;
                    case "issue invoice" :
                        $col .= '&nbsp;<a onclick="showInvoiceDialog(\'' . $rowIdentifierValue . '\');return false;" href="' . $link . '/' . $rowIdentifierKey . $rowIdentifierValue . '"><img title="' . strtoupper($name) . '"  src="' . $this->baseUrl . '/images/' . self::$invoiceButton . '" /></a>';
                        break;
                    case "view invoice" :
                        if($this->invoiceHelper->isAlreadyIssuedInvoice($rowIdentifierValue)){
                            $invoiceLink = $this->baseUrl . "/" . $this->invoiceHelper->getIssuedInvoiceLink($rowIdentifierValue);
                            $col .= '&nbsp;<a href="' . $invoiceLink . '"><img title="' . strtoupper($name) . '"  src="' . $this->baseUrl . '/images/' . self::$viewInvoiceButton . '" /></a>';
                        }
                        break;
                }
          }
          $col .= '</td>';

          return $col;
     }

     private function constructSortColLink($colName, $dbCol){
          if(in_array($dbCol, $this->sortColArr)){
               $link = '<a href="' . $this->view->url(array('controller' => $this->view->request()->getControllerName(), 'action' => $this->view->request()->getActionName(), Model_Constants_Constant::$SORT_URL_PARAM_NAME => $dbCol, Model_Constants_Constant::$SORTORDER_URL_PARAM_NAME => $this->getCurrentSortOrder($dbCol)), null, TRUE) . '">' . $colName . '</a>';
               return $link;
          }
          return $colName;
     }

     private function getCurrentSortColName(){
          $params = $this->view->request()->getAllParams();
          return $params[Model_Constants_Constant::$SORT_URL_PARAM_NAME];
     }

     private function getCurrentSortOrder($dbCol){
         $params = $this->view->request()->getAllParams();

         if(empty($params[Model_Constants_Constant::$SORTORDER_URL_PARAM_NAME])){
              return 'asc';
         }

         if($dbCol == $this->getCurrentSortColName()){
              if($params[Model_Constants_Constant::$SORTORDER_URL_PARAM_NAME] == 'asc'){
                   return 'desc';
              }else{
                   return 'asc';
              }
         }else{
              return 'asc';
         }

         return 'asc';
     }

     private function extractColumnPrefix($columnName){
           $colSplitArr = explode("_", $columnName);
           return $colSplitArr[0];
     }

     private function setDefaultSortColArr($headerColMap){
         $this->setSetSortColArr(array_values($headerColMap));
     }
}

?>