<?php

class Zend_View_Helper_SearchFormTable
{


    private $baseUrl;
    private $view;
    private $actionUrl;
    private $tableOptions = array('numOfCols' => 2);
    private $searchCriteria = array();

    public function searchFormTable($view)
    {
        $this->view = $view;
        $this->baseUrl = $view->baseUrl();
        $this->searchCriteria = $this->getSearchCriteriaValues();
        return $this;
    }

    public function getSearchFormTable(array $nameValueMap, $actionUrl = null, $tableOptions = null)
    {
        if(empty($actionUrl)){
              $this->actionUrl = $this->view->url(array('controller' => $this->view->request()->getControllerName(), 'action' => $this->view->request()->getActionName()), null, TRUE);
        }else{
            $this->actionUrl = $actionUrl;
        }


        if(!empty($tableOptions)){
            $this->tableOptions = $tableOptions;
        }

        $table = $this->constructTableBody($nameValueMap, $this->getTableFrame());

        return $this->getFormTable($table);
    }

    public function toString()
    {
        echo 'Search Table View Helper';
    }

    /************************************** Private Helper Functions *************************************/




    private function getTableFrame()
    {
        $tblFrame =
            '<table id="formTable">
            <thead>
            </thead>
            <tbody>
            </tbody>
         </table>';

        return $tblFrame;
    }

    private function getFormTable($table){
        $formFrame ='
           <form action="' . $this->actionUrl . '" method="post">' .
               $table
         . '</form>
        ';

        return $formFrame;
    }

    private function constructTableHeader()
    {

    }

    private function constructTableBody(array $nameValueMap, $table)
    {
         $numOfRows = ceil(count($nameValueMap)/ $this->tableOptions['numOfCols']);
         $tblRow = '';
         for($i = 0; $i < $numOfRows; $i++){
              $tblRow .= '<tr>';
                   $j = 0;
                   foreach($nameValueMap as $name => $value){
                         $tblRow .= '<td>' . $name . '</td><td>' . $this->getInputField($value) . '</td>';
                         $j++;
                         unset($nameValueMap[$name]);
                         if($j == $this->tableOptions['numOfCols']) break;
                   }
              $tblRow .= '</tr>';
         }

         $tblRow .= '<tr><td>' . $this->getFormSubmitButton() . '</td><td></td></tr>';

         return $this->installPart($table, 'tbody', $tblRow);
    }

    private function getFormSubmitButton(){
          $submit = '<input name="' . Model_Constants_Constant::$SEARCH_URL_PARAM_NAME . '" type="submit" value="' . Model_Constants_Constant::$SEARCH_URL_VALUE_NAME . '" />';
          return $submit;
    }

    private function getInputField($inputName){
          $input = '<input id="' . $inputName . '" name="' . Model_Constants_Constant::$SEARCH_CRITERIA_PARAM_NAME . '[' . $inputName . ']" value="' . $this->getInputValue($inputName) . '" />';
          return $input;
    }

    private function installPart($structure, $placeholder, $part)
    {
        $openTag = '<' . $placeholder . '>';
        $closeTag = '</' . $placeholder . '>';
        $openTagPos = strpos($structure, $openTag);
        $closeTagPos = strpos($structure, $closeTag);
        $firstPart = substr($structure, 0, $openTagPos + strlen($openTag));
        $secondPart = substr($structure, $closeTagPos, strlen($structure));

        return $firstPart . $part . $secondPart;
    }

    private function getSearchCriteriaValues(){
        $params = $this->view->request()->getAllParams();
        return $params[Model_Constants_Constant::$SEARCH_CRITERIA_PARAM_NAME];
    }

    private function getInputValue($key){
        return $this->searchCriteria[$key];
    }

}

?>