<?php

class Model_DbTable_Base extends Zend_Db_Table_Abstract
{

    protected $_parentTableMap = array();
    protected $_childTableMap = array();

    public function findAll(array $sort = null){
        $select = $this->select();

        if(!empty($sort)){
             $select->order($sort['name'] . ' ' . $sort['order']);
        }

        return $this->fetchAll($select)->toArray();
    }

    public function findAllByExactCriteria(array $searchCriteria, array $sort = null){
        $select = $this->select();

        if (!empty($sort)) {
            $select->order($sort['name'] . ' ' . $sort['order']);
        }

        $select = $this->populateSelectorWithSearchCriteria($select, $searchCriteria, true);

        return $this->fetchAll($select)->toArray();
    }

    public function findAllBySearchCriteria(array $searchCriteria, array $sort = null){
        $select = $this->select();

        if (!empty($sort)) {
            $select->order($sort['name'] . ' ' . $sort['order']);
        }

        $select = $this->populateSelectorWithSearchCriteria($select, $searchCriteria);

        return $this->fetchAll($select)->toArray();
    }

    public function findOneBySearchCriteria(array $searchCriteria, array $sort = null){

        $dataArr = $this->findAllBySearchCriteria($searchCriteria, $sort);
        return $dataArr[0];
    }

    public function findAllWithChildDataBySearchCriteria(array $searchCriteria, array $sort = null, $join = 'LEFT'){

        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()->from(array('parent' => $this->_name));

        if (!empty($this->_childTableMap)) {
            $i = 1;
            foreach ($this->_childTableMap as $tblName => $condition) {
                if(strtolower($join) == 'left'){
                    $select->joinLeft(array('child' . $i++ => $tblName), $condition);
                }elseif(strtolower($join) == 'right'){
                    $select->joinRight(array('child' . $i++ => $tblName), $condition);
                }elseif(strtolower($join) == 'inner'){
                    $select->joinInner(array('child' . $i++ => $tblName), $condition);
                }else{
                    $select->join(array('child' . $i++ => $tblName), $condition);
                }
            }
        }

        if (!empty($sort)) {
            $select->order($sort['name'] . ' ' . $sort['order']);
        }

        $select = $this->populateSelectorWithSearchCriteria($select, $searchCriteria);

        return $this->getAdapter()->fetchAll($select);
    }

    public function findAllWithParentDataBySearchCriteria(array $searchCriteria, array $sort = null){
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()->from(array('parent' => $this->_name));

        if (!empty($this->_parentTableMap)) {
            $i = 1;
            foreach ($this->_parentTableMap as $tblName => $condition) {
                $select->joinLeft(array('parent' . $i++ => $tblName), $condition);
            }
        }

        if (!empty($sort)) {
            $select->order($sort['name'] . ' ' . $sort['order']);
        }

        $select = $this->populateSelectorWithSearchCriteria($select, $searchCriteria);

        return $this->fetchAll($select)->toArray();
    }

    public function getFindAllSelector(array $sort = null){
        $select = $this->select();

        if (!empty($sort)) {
            $select->order($sort['name'] . ' ' . $sort['order']);
        }

        return $select;
    }

    public function getFindAllWithParentDataSelector(array $sort = null){
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()->from(array('parent' => $this->_name));

        if(!empty($this->_parentTableMap)){
             $i = 1;
             foreach($this->_parentTableMap as $tblName => $condition){
                  $select->joinLeft(array('parent' . $i++ => $tblName), $condition);
             }
        }

        if (!empty($sort)) {
            $select->order($sort['name'] . ' ' . $sort['order']);
        }

        return $select;
    }

    public function getFindOneWithParentDataSelector($id){
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()->from(array('parent' => $this->_name));

        if (!empty($this->_parentTableMap)) {
            $i = 1;
            foreach ($this->_parentTableMap as $tblName => $condition) {
                $select->joinLeft(array('parent' . $i++ => $tblName), $condition);
            }
        }

        $select = $this->populateSelectorWithSearchCriteria($select, array($this->_primary => $id));

        return $select;
    }

    public function populateSelectorWithSearchCriteria(Zend_Db_Select $selector, $searchCriteria, $exact=false){

        if($exact == true){
            foreach ($searchCriteria as $key => $value) {
                if (!empty($value)) {
                    $selector->where($key . ' = \'' . $value . '\'');
                }
            }
        }else{
            foreach ($searchCriteria as $key => $value) {
                if (!empty($value)) {
                    $selector->where($key . ' LIKE \'%' . $value . '%\'');
                }
            }
        }

        return $selector;
    }

    public function saveModels($modelObjectArr){
        $numRecordInserted = 0;
        foreach ($modelObjectArr as $k => $v) {
              $arrKeys = array_keys($v);
              $modelPrefix = $this->getModelPrefix($arrKeys);
              if(empty($v[$modelPrefix . "_Id"])){
                    $this->populationUniqueId($v,$modelPrefix);
                    $this->populateCreateInfo($v,$modelPrefix);
                    $lastInsertId = $this->insert($v);
                    if(!empty($lastInsertId)){
                          $numRecordInserted++;
                    }
              }
        }

        return $numRecordInserted;
    }

    public function saveModel($modelObject){
        $this->cleanModelData($modelObject);
        $arrKeys = array_keys($modelObject);
        $modelPrefix = $this->getModelPrefix($arrKeys);
        $v = $modelObject;
        if (empty($v[$modelPrefix . "_Id"])) {
            $this->populationUniqueId($v, $modelPrefix);
            $this->populateCreateInfo($v, $modelPrefix);
            $lastInsertId = $this->insert($v);
        }else{
            $lastInsertId = $v[$modelPrefix . "_Id"];
            $this->populateModifyInfo($v, $modelPrefix);
            $this->update($v, $modelPrefix . "_Id = '" . $v[$modelPrefix . "_Id"] . "'");
        }

        return $lastInsertId;
    }

    public function editModels($modelObjectArr){
        $numRecordUpdated = 0;
        foreach ($modelObjectArr as $k => $v) {
            $arrKeys = array_keys($v);
            $modelPrefix = $this->getModelPrefix($arrKeys);
            if (!empty($v[$modelPrefix . "_Id"])) {
                $this->populateModifyInfo($v, $modelPrefix);
                $numRowAffected = $this->update($v, $modelPrefix . "_Id = " . $v[$modelPrefix . "_Id"]);
                if (!empty($numRowAffected) && $numRowAffected != 0) {
                    $numRecordUpdated++;
                }
            }
        }

        return $numRecordUpdated;
    }


    public function editModel($modelObject){
        $this->cleanModelData($modelObject);
        $arrKeys = array_keys($modelObject);
        $modelPrefix = $this->getModelPrefix($arrKeys);
        $this->removeUnrelatedColumns($modelObject, $modelPrefix);
        $v = $modelObject;
        if (!empty($v[$modelPrefix . "_Id"])) {
            $this->populateModifyInfo($v, $modelPrefix);
            $numRowAffected = $this->update($v, $modelPrefix . "_Id = '" . $v[$modelPrefix . "_Id"] . "'");
        }

        return $numRowAffected;
    }

    public function getIdNameLookupMap($colArr)
    {
        $rows = $this->getIdNameLookupData($colArr);
        $map = $this->prepareIdNameLookupMap($rows);

        return $map;
    }

    public function getSelectionList($colArr){
        $rows = $this->getIdNameLookupData($colArr);
        return $this->prepareSelectionArray($rows);
    }

    protected function prepareIdNameLookupMap(array $rows)
    {
        $data = array();
        foreach ($rows as $key => $value) {
            $data[$value['Name']] = $value['Id'];
        }
        return $data;
    }

    protected function getIdNameLookupData($colArr)
    {
        $select = $this->select()->from($this->_name)->columns($colArr);
        $rows = $this->fetchAll($select)->toArray();

        return $rows;
    }


    /********** Private Helper Functions ***************************/

    private function getModelPrefix(array $keys){
          $key = $keys[0];
          $arr = explode("_", $key);
          $modelPrefix = $arr[0];
          return $modelPrefix;
    }

    private function populationUniqueId(&$v,$modelKey){
           $v[$modelKey . "_Id"] = uniqid($modelKey . "_");
    }

    private function populateCreateInfo(&$v,$modelKey){
           $v[$modelKey . "_CreatedBy"] = $this->getLoginUserId();
           //$v[$modelKey . "_CreatedOn"] = date('Y-m-d H:i:s');
    }

    private function populateModifyInfo(&$v, $modelKey){
        $v[$modelKey . "_ModifiedBy"] = $this->getLoginUserId();
        $v[$modelKey . "_ModifiedOn"] = date('Y-m-d H:i:s');
    }

    private function getLoginUserId(){
        $instance = Zend_Auth::getInstance();
        if($instance->hasIdentity()){
              $loginUser = $instance->getStorage()->read();
              return $loginUser->Usr_Id;
        }else{
             return "SYSTEM";
        }
    }

    private function prepareSelectionArray(array $rows){
        $data = array("" => 'Please Select');
        foreach ($rows as $key => $value) {
            $data[$value['value']] = $value['text'];
        }
        return $data;
    }

    private function cleanModelData(&$modelObject){
        foreach(Model_Constants_DataMap::$INVALID_COLUMN_NAMES as $column){
             if(!empty($modelObject[$column])){
                  unset($modelObject[$column]);
             }
        }
    }

    private function removeUnrelatedColumns(&$modelObject, $modelPrefix = null){
        if (!empty($modelPrefix)) {
            foreach ($modelObject as $key => $value) {
                $prefix = $this->getModelPrefix(array($key));
                if ($prefix != $modelPrefix) {
                    unset($modelObject[$key]);
                }
            }
        }
    }

}

?>