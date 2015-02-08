<?php

class BalanceTree {

    private $inputString;


    public function __construct($inputString){
         $this->inputString = $inputString;
    }

    public function parseTree(){
        // split by new line .. of course that will depends on how input lines are delimited
        $strArr = explode("\r\n", $this->inputString);

        $numOfTests = $strArr[0];

        for ($i = 1; $i <= $numOfTests; $i++) {
            $eachTest = $strArr[$i];
            $bTreeArray = $this->convertInputStringToArray($eachTest);
            print_r($bTreeArray);

            // get total weight on left branch
            $leftTotalWeight = $this->getTotalWeight($bTreeArray[0]);

            // get total weight on right branch
            $rightTotalWeight = $this->getTotalWeight($bTreeArray[1]);

            if ($this->isBalancedTree($bTreeArray) AND ($leftTotalWeight == $rightTotalWeight)) {
                echo 'YES';
            } else {
                echo 'NO';
            }
            echo '<br />';
        }

        exit;
    }

    /*
     * @param array $bTreeArray   Tree to be traversed find out if it is balanced or not
     *
     * Recursive method to find out if a tree is balanced or not.
     *
     * Caution : [2,[2,2]]  is balanced tree, but the weights on left and right pans not equal .. so the main balance will not be stabilized
     *
     * But [4,[2,2]] is both balanced tree , and also weights on left and right pans are equal .. so the main balance will be stabilized
     *
     */
    private function isBalancedTree($bTreeArray){
        if (empty($bTreeArray) OR !is_array($bTreeArray)) {
            return true;
        }

        if (!is_array($bTreeArray[0]) AND !is_array($bTreeArray[1]) AND ($bTreeArray[0] == $bTreeArray[1])) {
            return true;
        }

        if (!is_array($bTreeArray[0]) AND !is_array($bTreeArray[1]) AND ($bTreeArray[0] != $bTreeArray[1])) {
            return false;
        }

        if (($this->isBalancedTree($bTreeArray[0]) == $this->isBalancedTree($bTreeArray[1]))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $eachTest String
     * @return array
     *
     * Given [4,[2,2]] , this function will return Array(0 => 4, 1 => Array(0 => 2, 1 => 2))
     */
    private function convertInputStringToArray($eachTest){
        $eachTest = str_replace("[", "array(", $eachTest);
        $arrString = str_replace("]", ")", $eachTest);
        $bTreeArray = '$bTreeArray = ' . $arrString . ';';
        eval($bTreeArray);

        return $bTreeArray;
    }

    /*
     * @param array $bTreeArray
     *
     * Recursive method to find out the total weight on a tree
     *
     * [3,8]  : 11
     * [5,[4,4]] : 13
     *
     */
    private function getTotalWeight($bTreeArray){
        if (empty($bTreeArray)) {
            return 0;
        }

        if (!is_array($bTreeArray)) {
            return $bTreeArray;
        }

        return $this->getTotalWeight($bTreeArray[0]) + $this->getTotalWeight($bTreeArray[1]);
    }

    public function getInputString(){
        return $this->inputString;
    }
}

$inputString = '5
[100,100]
[5,[2.5,2.5]]
[[[3,2],5],10]
[[[5,5],10],[10,10]]
[[4,[2,2]],[4,4]]';

$btree = new BalanceTree($inputString);
$btree->parseTree();

?>