<?php
class Model_DbTable_Buildings extends Model_DbTable_Base
{
    protected $_name = 'tbl_buildings';
    protected $_primary = 'Bul_Id';

    protected $_referenceMap = array(
        'tbl_buildingoperators' => array(
            'columns' => array('Bul_OperatorId'),
            'refTableClass' => 'Model_DbTable_BuildingOperators',
            'refColumns' => array('Buo_Id')
        )
    );

    protected $_parentTableMap = array(
        'tbl_buildingoperators' => 'Bul_OperatorId = Buo_Id'
    );

    public function saveBuildings($buildingData)
    {
        $this->saveModels($buildingData);
    }

}

?>