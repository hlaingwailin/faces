<?php
class Model_DbTable_Deposits extends Model_DbTable_Base
{
    protected $_name = 'tbl_deposits';
    protected $_primary = 'Dep_Id';

    protected $_parentTableMap = array(
        'tbl_shops' => 'Dep_ShopId = Sho_Id'
    );

    public function saveDeposits($depositData)
    {
        $this->saveModels($depositData);
    }
}

?>