<?php
class Model_DbTable_Rates extends Model_DbTable_Base
{
    protected $_name = 'tbl_rates';
    protected $_primary = 'Rat_Id';

    protected $_parentTableMap = array();

    protected $_childrenTableMap = array();

    public function saveRateData($rateData)
    {
        $this->saveModels($rateData);
    }
}

?>