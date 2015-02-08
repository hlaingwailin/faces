<?php
class Model_DbTable_User extends Model_DbTable_Base
{
    protected $_name = 'tbl_users';
    protected $_primary = 'Usr_Id';

    public function loadDefaultUsers($userData){
         $this->saveModels($userData);
    }

}

?>