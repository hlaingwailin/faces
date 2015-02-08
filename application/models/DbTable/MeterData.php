<?php
class Model_DbTable_MeterData extends Model_DbTable_Base
{
    protected $_name = 'tbl_meterdata';
    protected $_primary = 'Met_Id';

    protected $_parentTableMap = array(
        'tbl_shops' => 'Met_CustomerId = Sho_CustomerId'
    );

    protected $_childrenTableMap = array();

    public function saveMeterData($meterData)
    {
        $this->saveModels($meterData);
    }

    public function populateSelectorWithBatchInvoiceRunParameters(Zend_Db_Select $selector, $bathNumber, $buildingId){
        //$selector->where(new Zend_Db_Expr("DATE_FORMAT(Met_CurrentUsageEndDate,'%y%m') = '" . $bathNumber . "'"));
        $selector->where("Met_BatchId = '" . $bathNumber . "'");
        $selector->where("Sho_BuildingId = '" . $buildingId . "'");
        $selector->where("Sho_Terminated = 0");

        return $selector;
    }

    public function getMaxInvoiceRunningNumber($selector,$bathNumber, $buildingId){
        $selector = $this->select(self::SELECT_WITH_FROM_PART)->from(array("met" => $this->_name))->columns(array("currentRunningNumber" => new Zend_Db_Expr("max(met.Met_InvoiceRunningNumber)")));
        $selector->where(new Zend_Db_Expr("DATE_FORMAT(met.Met_CurrentUsageEndDate,'%y%m') = '" . $bathNumber . "'"));
        //$selector->where("tbl_shops.Sho_BuildingId = '" . $buildingId . "'");

        return $this->getAdapter()->fetchRow($selector);
    }
}

?>