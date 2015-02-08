<?php



class My_Action_Helper_Security extends Zend_Controller_Action_Helper_Abstract
{
    private $auth;

    function __construct()
    {
        $this->auth = Zend_Auth::getInstance();
    }

    function isAuthenticatedAdmin()
    {
        if ($this->auth->getStorage()->read()->Usr_Role == 'admin') {
            return true;
        }

        return false;
    }

    function getCurrentLoginUser()
    {
        return $this->auth->getStorage()->read();
    }
}

?>