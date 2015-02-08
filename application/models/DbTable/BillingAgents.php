<?php
class Model_DbTable_BillingAgents extends Model_DbTable_Base
{
    protected $_name = 'tbl_billingagents';
    protected $_primary = 'Agn_Id';

    protected $_dependentTables = array();

    public function saveBillingAgents($agentData)
    {
        $this->saveModels($agentData);
    }
}

?>