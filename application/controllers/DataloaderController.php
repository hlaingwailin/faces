<?php
class DataloaderController extends Zend_Controller_Action
{
    public function init()
    {
        $this->getHelper('layout')->setLayout('admin');
    }

    public function loaddataAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $tblUser = new Model_DbTable_User();
        $tblUser->loadDefaultUsers($this->getDefaultUsers());

    }

    private function getDefaultUsers(){
        $user['Usr_Email'] = "hlaingwailin@gmail.com";
        $user['Usr_Password'] = md5("p@ssw0rd!!");
        $user['Usr_Role'] = "admin";
        $users[] = $user;

        return $users;
    }

}

?>