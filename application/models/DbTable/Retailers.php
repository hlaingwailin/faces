<?php
class Model_DbTable_Retailers extends Model_DbTable_Base
{
    protected $_name = 'tbl_retailers';
    protected $_primary = 'Ret_Id';

    protected $_dependentTables = array();

    public function saveRetailers($retailerData)
    {
        $this->saveModels($retailerData);
    }
}

?>