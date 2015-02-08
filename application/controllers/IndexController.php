<?php
class IndexController extends Zend_Controller_Action
{

    public function init(){

    }

    public function indexAction()
    {
        $this->getHelper('layout')->setLayout('index');

        $inputString = '4
[5,5]
[5,[2,2]]
[[[3,2],5],10]
[[[5,5],10],[10,10]]';

    }

    public function testAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $inputString = '1
';

        $strArr = explode("\r\n", $inputString);

        $numOfTests = $strArr[0];

        for($i = 1; $i <= $numOfTests; $i++){
            $weights = $strArr[$i];
            $weights = str_replace("[", "array(", $weights);
            $arrString = str_replace("]", ")", $weights);
            $bTreeArray = '$bTreeArray = ' . $arrString . ';';
            eval($bTreeArray);print_r($bTreeArray);

            $leftTotalWeight = $this->getTotalWeight($bTreeArray[0]);
            $rightTotalWeight = $this->getTotalWeight($bTreeArray[1]);

            if($this->isBalancedTree($bTreeArray) AND ($leftTotalWeight == $rightTotalWeight)){
                 echo 'YES';
            }else{
                echo 'NO';
            }
            echo '<br />';
        }

        exit;
    }

    private function isBalancedTree($binaryTreeArray){
       // print_r((array)$binaryTreeArray);echo $binaryTreeArray[0] . '  ' . $binaryTreeArray[1];exit;

        if(empty($binaryTreeArray) OR !is_array($binaryTreeArray)){
            return true;
        }

        if(!is_array($binaryTreeArray[0]) AND !is_array($binaryTreeArray[1]) AND ($binaryTreeArray[0] == $binaryTreeArray[1])){
            return true;
        }

        if (!is_array($binaryTreeArray[0]) AND !is_array($binaryTreeArray[1]) AND ($binaryTreeArray[0] != $binaryTreeArray[1])) {
            return false;
        }

        if(($this->isBalancedTree($binaryTreeArray[0]) == $this->isBalancedTree($binaryTreeArray[1]))){
            return true;
        }else{
            return false;
        }
    }

    private function getTotalWeight($binaryTreeArray){

        if (empty($binaryTreeArray)) {
            return 0;
        }

        if(!is_array($binaryTreeArray)){
            return $binaryTreeArray;
        }

        return $this->getTotalWeight($binaryTreeArray[0]) + $this->getTotalWeight($binaryTreeArray[1]);
    }

}

?>