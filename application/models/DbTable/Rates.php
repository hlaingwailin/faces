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

    public function getRateForSpecificDate($date){

        $select = $this->select();
        $select->where('Rat_StartDate <= "' . $date . '"');
        $select->order('Rat_StartDate DESC');
        $select->limit(1);

        $rates = $this->fetchAll($select)->toArray();
        return $rates[0];
    }
}

?>