<?php
class Model_DbTable_Companies extends Model_DbTable_Base
{
    protected $_name = 'tbl_shopoperators';
    protected $_primary = 'Sop_Id';

    public function saveCompanies($companyData)
    {
        $this->saveModels($companyData);
    }
}

?>