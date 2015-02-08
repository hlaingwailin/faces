<?php
class Model_DbTable_Shops extends Model_DbTable_Base
{
    protected $_name = 'tbl_shops';
    protected $_primary = 'Sho_Id';

    protected $_parentTableMap = array(
        'tbl_shopoperators' => 'Sho_OperatorId = Sop_Id',
        'tbl_buildings' => 'Sho_BuildingId = Bul_Id'
    );

    public function saveShops($shopData)
    {
        $this->saveModels($shopData);
    }
}

?>