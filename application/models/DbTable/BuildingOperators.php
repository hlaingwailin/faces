<?php
class Model_DbTable_BuildingOperators extends Model_DbTable_Base
{
    protected $_name = 'tbl_buildingoperators';
    protected $_primary = 'Buo_Id';

    protected $_dependentTables = array('tbl_buildings');

    public function saveBuildingOperators($operatorData)
    {
        $this->saveModels($operatorData);
    }

}

?>